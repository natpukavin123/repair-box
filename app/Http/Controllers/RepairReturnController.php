<?php

namespace App\Http\Controllers;

use App\Models\{Repair, RepairReturn, RepairReturnItem, LedgerTransaction, ActivityLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepairReturnController extends Controller
{
    public function create(Repair $repair)
    {
        // Check total IN payments cover the grand total (ignore refunds — they happen after returns)
        if ($repair->total_paid < $repair->grand_total) {
            abort(403, 'Returns can only be processed for fully paid repairs.');
        }

        $repair->load('customer', 'parts.part', 'repairServices.vendor', 'repairServices.serviceType', 'repairReturns.items');

        // Calculate already returned quantities
        $returnedParts = [];
        $returnedServices = [];
        foreach ($repair->repairReturns as $ret) {
            foreach ($ret->items as $item) {
                if ($item->item_type === 'part' && $item->repair_part_id) {
                    $returnedParts[$item->repair_part_id] = ($returnedParts[$item->repair_part_id] ?? 0) + $item->quantity;
                }
                if ($item->item_type === 'service' && $item->repair_service_id) {
                    $returnedServices[$item->repair_service_id] = true;
                }
            }
        }

        // Check if there are any returnable items left
        $hasReturnableParts = $repair->parts->contains(fn($rp) => $rp->quantity - ($returnedParts[$rp->id] ?? 0) > 0);
        $hasReturnableServices = $repair->repairServices->contains(fn($svc) => !isset($returnedServices[$svc->id]));
        $hasReturnableItems = $hasReturnableParts || $hasReturnableServices;

        return view('modules.repairs.returns.create', compact('repair', 'returnedParts', 'returnedServices', 'hasReturnableItems'));
    }

    public function store(Request $request, Repair $repair)
    {
        // Check total IN payments cover the grand total (ignore refunds)
        if ($repair->total_paid < $repair->grand_total) {
            return response()->json(['success' => false, 'message' => 'Returns can only be processed for fully paid repairs.'], 422);
        }

        $data = $request->validate([
            'reason' => 'required|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:part,service',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.return_amount' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($repair, $data) {
            $repair->load('parts.part', 'repairServices', 'repairReturns.items');

            // Calculate already returned quantities
            $returnedParts = [];
            $returnedServices = [];
            foreach ($repair->repairReturns as $ret) {
                foreach ($ret->items as $item) {
                    if ($item->item_type === 'part' && $item->repair_part_id) {
                        $returnedParts[$item->repair_part_id] = ($returnedParts[$item->repair_part_id] ?? 0) + $item->quantity;
                    }
                    if ($item->item_type === 'service' && $item->repair_service_id) {
                        $returnedServices[$item->repair_service_id] = true;
                    }
                }
            }

            $totalReturnAmount = 0;
            $returnItems = [];

            foreach ($data['items'] as $item) {
                if ($item['type'] === 'part') {
                    $rp = $repair->parts->find($item['id']);
                    if (!$rp)
                        continue;

                    $alreadyReturned = $returnedParts[$rp->id] ?? 0;
                    $maxQty = $rp->quantity - $alreadyReturned;
                    if ($item['quantity'] > $maxQty)
                        continue;

                    $returnItems[] = [
                        'item_type' => 'part',
                        'repair_part_id' => $rp->id,
                        'item_name' => $rp->part ? $rp->part->name : 'Part',
                        'quantity' => $item['quantity'],
                        'unit_price' => $rp->cost_price,
                        'return_amount' => $item['return_amount'],
                        'reason' => $item['reason'] ?? null,
                    ];
                    $totalReturnAmount += $item['return_amount'];
                } else {
                    $svc = $repair->repairServices->find($item['id']);
                    if (!$svc || isset($returnedServices[$svc->id]))
                        continue;

                    $returnItems[] = [
                        'item_type' => 'service',
                        'repair_service_id' => $svc->id,
                        'item_name' => $svc->service_type_name,
                        'quantity' => 1,
                        'unit_price' => $svc->customer_charge,
                        'return_amount' => $item['return_amount'],
                        'reason' => $item['reason'] ?? null,
                    ];
                    $totalReturnAmount += $item['return_amount'];
                }
            }

            if (empty($returnItems)) {
                throw new \Exception('No valid items to return.');
            }

            $return = RepairReturn::create([
                'return_number' => RepairReturn::generateReturnNumber(),
                'repair_id' => $repair->id,
                'customer_id' => $repair->customer_id,
                'reason' => $data['reason'],
                'total_return_amount' => $totalReturnAmount,
                'status' => 'confirmed',
                'created_by' => auth()->id(),
            ]);

            foreach ($returnItems as $ri) {
                $return->items()->create($ri);
            }

            // Return parts to stock
            foreach ($return->items()->where('item_type', 'part')->get() as $returnItem) {
                // Note: In this system, Parts are distinct from Inventory Products.
                // We do not create StockMovements or update Inventory for Parts.
            }

            ActivityLog::log(
                'create',
                'repair_returns',
                $return->id,
                "Created return {$return->return_number} for repair {$repair->ticket_number} — ₹" . number_format($totalReturnAmount, 2)
            );

            return response()->json([
                'success' => true,
                'message' => "Return {$return->return_number} created successfully",
                'redirect' => "/repairs/{$repair->id}/returns/{$return->id}",
            ]);
        });
    }

    public function show(Repair $repair, RepairReturn $return)
    {
        if ($return->repair_id !== $repair->id)
            abort(404);

        $return->load('items.repairPart.part', 'items.repairService', 'customer', 'creator');
        $repair->load('customer', 'parts.part', 'repairServices', 'payments', 'repairReturns.items');

        // Calculate already returned quantities across ALL returns
        $returnedParts = [];
        $returnedServices = [];
        foreach ($repair->repairReturns as $ret) {
            foreach ($ret->items as $item) {
                if ($item->item_type === 'part' && $item->repair_part_id) {
                    $returnedParts[$item->repair_part_id] = ($returnedParts[$item->repair_part_id] ?? 0) + $item->quantity;
                }
                if ($item->item_type === 'service' && $item->repair_service_id) {
                    $returnedServices[$item->repair_service_id] = true;
                }
            }
        }

        $hasReturnableParts = $repair->parts->contains(fn($rp) => $rp->quantity - ($returnedParts[$rp->id] ?? 0) > 0);
        $hasReturnableServices = $repair->repairServices->contains(fn($svc) => !isset($returnedServices[$svc->id]));
        $hasReturnableItems = $hasReturnableParts || $hasReturnableServices;

        return view('modules.repairs.returns.show', compact('repair', 'return', 'hasReturnableItems'));
    }

    public function processRefund(Request $request, Repair $repair, RepairReturn $return)
    {
        if ($return->repair_id !== $repair->id)
            abort(404);

        if ($return->status === 'refunded') {
            return response()->json(['success' => false, 'message' => 'This return has already been refunded.'], 422);
        }

        $data = $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $return->total_return_amount,
            'refund_method' => 'required|string|in:cash,upi,bank_transfer,card',
            'refund_reference' => 'nullable|string|max:100',
            'refund_notes' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($repair, $return, $data) {
            $return->update([
                'refund_amount' => $data['refund_amount'],
                'refund_method' => $data['refund_method'],
                'refund_reference' => $data['refund_reference'] ?? null,
                'refund_notes' => $data['refund_notes'] ?? null,
                'status' => 'refunded',
                'refunded_at' => now(),
            ]);

            // Record as OUT payment on the repair
            $repair->payments()->create([
                'payment_type' => 'refund',
                'amount' => $data['refund_amount'],
                'payment_method' => $data['refund_method'],
                'reference_number' => $data['refund_reference'] ?? null,
                'direction' => 'OUT',
                'notes' => "Return refund ({$return->return_number}): " . ($data['refund_notes'] ?? ''),
            ]);

            LedgerTransaction::create([
                'transaction_type' => 'repair',
                'reference_module' => 'repair_returns',
                'reference_id' => $return->id,
                'amount' => $data['refund_amount'],
                'payment_method' => $data['refund_method'],
                'direction' => 'OUT',
                'description' => "Refund for return {$return->return_number} against repair {$repair->ticket_number}",
                'created_by' => auth()->id(),
            ]);

            ActivityLog::log(
                'update',
                'repair_returns',
                $return->id,
                "Refund processed for {$return->return_number} — ₹" . number_format($data['refund_amount'], 2) . " via {$data['refund_method']}"
            );

            return response()->json([
                'success' => true,
                'message' => 'Refund of ₹' . number_format($data['refund_amount'], 2) . ' processed successfully',
            ]);
        });
    }

    public function invoice(Repair $repair, RepairReturn $return)
    {
        if ($return->repair_id !== $repair->id)
            abort(404);

        $return->load('items.repairPart.part', 'items.repairService', 'customer', 'creator');
        $repair->load('customer', 'parts.part', 'repairServices', 'repairReturns.items');

        // Total already returned across all returns for this repair
        $totalAlreadyReturned = $repair->repairReturns->sum('total_return_amount');

        // Check if more items can still be returned
        $returnedParts = [];
        $returnedServices = [];
        foreach ($repair->repairReturns as $ret) {
            foreach ($ret->items as $item) {
                if ($item->item_type === 'part' && $item->repair_part_id) {
                    $returnedParts[$item->repair_part_id] = ($returnedParts[$item->repair_part_id] ?? 0) + $item->quantity;
                }
                if ($item->item_type === 'service' && $item->repair_service_id) {
                    $returnedServices[$item->repair_service_id] = true;
                }
            }
        }
        $hasReturnableParts = $repair->parts->contains(fn($rp) => $rp->quantity - ($returnedParts[$rp->id] ?? 0) > 0);
        $hasReturnableServices = $repair->repairServices->contains(fn($svc) => !isset($returnedServices[$svc->id]));
        $hasReturnableItems = $hasReturnableParts || $hasReturnableServices;

        return view('modules.repairs.returns.invoice', compact('repair', 'return', 'hasReturnableItems', 'totalAlreadyReturned'));
    }
}

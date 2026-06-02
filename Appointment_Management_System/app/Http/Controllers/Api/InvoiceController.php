<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\CreateInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\In;

class InvoiceController extends Controller
{
    public function index()
    {
        try{
        $invoices = Invoice::latest()->paginate(10);
        return response()->json([
            'status' => true,
            'message' => 'Invoice List',
            'invoices' => $invoices,
        ],200);

        } catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch invoice list',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function store(CreateInvoiceRequest $request)
    {
        $data = $request->validated();
        try{
            $invoice = Invoice::create([
                'business_code' => $request->business_code,
                'appointment_code' => $request->appointment_code,
                'subtotal' => $request->subtotal,
                'total' => $request->total,
                'status' => $request->status ?? 'draft',
                'invoice_date' => $request->invoice_date,
                'updated_by_code' => auth()->user()->code,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Invoice Created Successfully',
                'invoice' => $invoice,
            ],200);

        } catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function show(Invoice $invoice)
    {
        try{

        if(!$invoice){
            return response()->json([
                'status' => false,
                'message' => 'Invoice Not Found',
                'error' => 'Invoice Not Found',
            ],404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Invoice Details',
            'invoice' => $invoice,
        ],200);

        } catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Failed to get invoice',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        //dd($request->validated());
        $data = $request->validated();
        try{
            if(!$invoice){
                return response()->json([
                    'status' => false,
                    'message' => 'Invoice Not Found',
                ],404);
            }

            $invoice->update($request->validated());
            return response()->json([
                'status' => true,
                'message' => 'Invoice Updated Successfully',
                'invoice' => $invoice,
            ],201);

        } catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Failed to update invoice',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function destroy(Invoice $invoice)
    {
        try{
            if(!$invoice){
                return response()->json([
                    'status' => false,
                    'message' => 'Invoice Not Found',
                ],401);
            }
            $invoice->delete();

            return response()->json([
                'status' => true,
                'message' => 'Invoice Deleted Successfully',
                'invoice' => $invoice,
            ],200);

        } catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete invoice',
                'error' => $e->getMessage(),
            ],401);
        }
    }
}

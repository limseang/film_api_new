<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\AlibabaStorage;
use App\Http\DataTables\ReportIncomeExpenseDataTable;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ReportIncomeExpense;
use App\Constant\RolePermissionConstant;

class ReportIncomeExpenseController extends Controller
{
    
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

   
    public function index(ReportIncomeExpenseDataTable $dataTable)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_REPORT_INCOME_EXPENSE_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $groupByStatus = ReportIncomeExpense::select('type','currency', DB::raw('SUM(amount) as total'))->groupBy('type','currency')->get() ?? [];
        $data['groupByStatus'] = $groupByStatus;
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.report_income_expense')]];
        return $dataTable->render('report_income_expense.index', $data);
    }

    public function create()
    {
        if(!authorize(RolePermissionConstant::PERMISSION_REPORT_INCOME_EXPENSE_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('gift.index'), 'page' => __('sma.report_income_and_expense')], ['link' => '#', 'page' => __('sma.add')]];
        return view('report_income_expense.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'reference' => 'nullable',
            'type' => 'required|in:1,2',
            'amount' => 'required|numeric',
            'currency' => 'required|in:USD,KHR',
            'created_at' => 'required',
            'noted' => 'nullable',
            'attachment' => 'nullable',
        ]);
        try{
            DB::beginTransaction();
            $reportIncomeExpense = new ReportIncomeExpense();
            if($request->hasFile('attachment')){
                $image = $this->UploadFile($request->file('attachment'), 'ReportIncomeExpense');
            }
            $convertedDate = Carbon::createFromFormat('d/m/Y H:i:s', time: $request->created_at)->format('Y-m-d H:i:s');
            $reportIncomeExpense->name = $request->name;
            $reportIncomeExpense->reference = $request->reference;
            $reportIncomeExpense->type = $request->type;
            $reportIncomeExpense->amount = $request->amount;
            $reportIncomeExpense->currency = $request->currency;
            $reportIncomeExpense->created_at = $convertedDate;
            $reportIncomeExpense->date_at = $convertedDate;
            $reportIncomeExpense->noted = $request->noted;
            $reportIncomeExpense->attachment = $image ?? '';
            $reportIncomeExpense->created_by = auth()->user()->id;
            $reportIncomeExpense->save();
           
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('report_income_expense.'.$pageDirection)->with($notification);
        }catch(Exception $e){
            DB::rollBack();
            $notification = [
                'type' => 'exception',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => $e->getMessage()
            ];
            return redirect()->back()->withInput()->with($notification);
        }
    }

    public function edit($id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_REPORT_INCOME_EXPENSE_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['reportIncomeExpense'] = ReportIncomeExpense::find($id);
        if(!$data['reportIncomeExpense']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->back()->with($notification);
        }
        $data['image'] = $this->getSignUrlNameSize($data['reportIncomeExpense']->attachment);
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('cast.index'), 'page' => __('sma.cast')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('report_income_expense.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'reference' => 'nullable',
            'type' => 'required|in:1,2',
            'amount' => 'required|numeric',
            'currency' => 'required|in:USD,KHR',
            'created_at' => 'required',
            'noted' => 'nullable',
            'attachment' => 'nullable',

        ]);
        try{
            DB::beginTransaction();
            $reportIncomeExpense = ReportIncomeExpense::find($id);
            if(!$reportIncomeExpense){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' =>  trans('sma.the_not_exist')
                ];
                return redirect()->back()->with($notification);
            }

            if($request->hasFile('attachment')){
                $image = $this->UploadFile($request->file('attachment'), 'ReportIncomeExpense');
                $reportIncomeExpense->attachment = $image;
            }
            $convertedDate = Carbon::createFromFormat('d/m/Y H:i:s', time: $request->created_at)->format('Y-m-d H:i:s');
            $reportIncomeExpense->name = $request->name;
            $reportIncomeExpense->reference = $request->reference;
            $reportIncomeExpense->type = $request->type;
            $reportIncomeExpense->amount = $request->amount;
            $reportIncomeExpense->currency = $request->currency;
            $reportIncomeExpense->created_at = $convertedDate;
            $reportIncomeExpense->date_at = $convertedDate;
            $reportIncomeExpense->noted = $request->noted;
            $reportIncomeExpense->updated_by = auth()->user()->id;
            $reportIncomeExpense->save();
            DB::commit();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('report_income_expense.index')->with($notification);
        }catch(Exception $e){
            DB::rollBack();
            $notification = [
                'type' => 'exception',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => $e->getMessage()
            ];
            return redirect()->back()->withInput()->with($notification);
            }
        }

        public function destroy($id)
        {
            if(!authorize(RolePermissionConstant::PERMISSION_REPORT_INCOME_EXPENSE_DELETE)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $reportIncomeExpense = ReportIncomeExpense::find($id);
            if(!$reportIncomeExpense){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('cast.index')->with($notification);
            }
            $reportIncomeExpense->delete(); 
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.delete_successfully'),
            ];
            return redirect()->back()->with($notification);
        }

       
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemLog;
use App\Http\DataTables\SystemLogDataTable;
use App\Constant\RolePermissionConstant;

class SystemLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('lang');
    }
    public function index(SystemLogDataTable $dataTable)
    { 
        if(!authorize(RolePermissionConstant::PERMISSION_SYSTEM_LOG_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => '#', 'page' => __('sma.system_user_log')]];
        return $dataTable->render('system_log.index', $data);
    }

    public function showDetail(Request $request)
    {
        $logs = SystemLog::where('id',$request->log_id)->select('properties','event','description')->first();
        $properties = json_decode($logs->properties,true);
        $trs ='';
        $text = '';
        if ($logs->description == 'updated') {
            $trs .='<tr class=" bg-info bg-opacity-30">
                <th colspan="2">CURRENT</th>
                <th colspan="2">OLD</th>
            </tr>';
        }else{
            $trs .='<tr class="bg-info bg-opacity-30">
            <th colspan="2">CURRENT</th>
        </tr>';
        }
        if (!empty($properties['attributes'])) {
            foreach ($properties['attributes'] as $title => $value) {
                $old = '';
                if(!empty($properties['old']) ){
                    $old = $properties['old'][$title];
                }
                $text = $title;
                $tesxt = "Short_name";
                $pos = strpos($tesxt,"_");
                $titleArr =[];
                if ($pos) {
                  $titleArr =   explode('_', $title);
                  $text ='';
                  foreach($titleArr as $a){
                        $text .= ''.$a.' ';
                  }
                }
                $trs .='<tr>
                        <td class="">'.ucfirst($text).' </td>
                        <td class="">: '.$value.'</td>';
                        if($logs->description=='updated'){
                            $trs .='<td class="text-danger">   '.ucfirst($text).' </td>
                                    <td class="text-danger">: '.$old.'</td>';
                        }
                $trs .='</tr>';
            }
        }
        $data['trs']  = $trs;
        $data['event']  = ucfirst($logs->description);
        return response()->json($data);
    }
}

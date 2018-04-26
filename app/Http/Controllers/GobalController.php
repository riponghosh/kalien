<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;

class GobalController extends Controller
{

    public function __construct(){

    }

    public function change_web_language(Request $request){
        $lans = ['zh_tw','en', 'jp'];
        if(in_array($request->language,$lans)){
            return redirect()->back()->cookie('web_language',$request->language);
        }
        return redirect()->back();
    }

    public function change_web_cur_unit(Request $request){

        $units = ['HKD', 'TWD', 'JPY'];
		if(in_array($request->unit,$units)) {
			return redirect()->back()->cookie('currency_unit',$request->unit);
		}
		return redirect()->back();
    }
}
?>


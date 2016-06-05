<?php
class fenxiAction extends Action {
	public function _initialize() {
		// parent::_initialize();
		header('Content-Type:text/html; charset=utf-8');
		set_time_limit(0);
	}

	public function index(){
		//
		$shuma='898,1093,896,897,992,895,137,145,1832,1833,1830,1324,1924,1840,1916,1856,1848,2084,1421,1555,2013,1987,1923';

		$sarr=explode(',', $shuma);
		// dump($sarr);
		$all=array();
		foreach($sarr as $sm){

			//jiang
			$data['jiang']=$sm;
			$data['zongqishu']=M('jiang')->where('jiangid='.$sm)->count();

			array_push($all, $data);
		}

		dump($all);
	}

	public function meici(){
		$list=M('jiang')->where('jiangid=898')->select();
		$all=array();
		foreach($list as $jj){
			$data['qishu']=$jj['qiid'];
			$data['zongrenshu']=M('canjia')->where('jiangid='.$jj['id'])->count();
			$zjinfo=M('canjia')->where('jiangid='.$jj['id'].' and uid='.$jj['zhonguid'])->find();
			$data['cishu']=$zjinfo['cishu'];
			array_push($all, $data);
		}

		dump($all);
	}
}











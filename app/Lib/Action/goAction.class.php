<?php
class goAction extends Action {
	public function _initialize() {
		// parent::_initialize();
		header('Content-Type:text/html; charset=utf-8');
		set_time_limit(0);
	}

	public function shuma(){
		$shuma='898,1093,896,897,992,895,137,145,1832,1833,1830,1324,1924,1840,1916,1856,1848,2084,1421,1555,2013,1987,1923';

		$shumaarr=explode(',', $shuma);
foreach($shumaarr as $ssm){
		$list=M('jiang')->where('quren_status=0 and zj_status=1 and jiangid='.$ssm)->order('id asc')->select();
		foreach($list as $jj){
			$url=$this->buildurl($jj, 1);
			// dump($url);
			$res=file_get_contents($url);
			$resarr=json_decode($res, true);
			// dump($resarr);
			if($resarr['code']==0){
				foreach($resarr['result']['list'] as $canjia){
					$this->addcanjia($canjia, $jj['id']);
				}
			}
			//处理其他页数
			$yeshu=ceil($resarr['result']['totalCnt']/50);
			// dump($yeshu);
			if($yeshu>=2){
				for($yy=2; $yy<=$yeshu;$yy++){
					$url=$this->buildurl($jj, $yy);
					// dump($url);
					$res=file_get_contents($url);
					$resarr=json_decode($res, true);
					// dump($resarr);
					if($resarr['code']==0){
						foreach($resarr['result']['list'] as $canjia){
							$this->addcanjia($canjia, $jj['id']);
						}
					}		
				}
			}
			$sdata['quren_status']=1;
			M('jiang')->where('id='.$jj['id'])->save($sdata);
		}
}
	}

	public function index(){
		do{
			$this->jiang();
			$this->gaoren();
		}while(1);
	}

	public function zjzj(){
		$list=M('jiang')->where('zj_status=0')->order('id asc')->select();
		Vendor('simple_html_dom');
		foreach($list as $jj){
			$url=$this->buildpageurl($jj);
			$res=file_get_contents($url);
			$dom=file_get_sal($res);

			$zjzj=$dom->find('.m-detail-main-winner-detail',0);
			if($zjzj->innertext){
				// dump($zjzj->innertext);
				$uu = $zjzj->find('.user-id .bd', 0);
				$hasu=trim($uu->innertext);

				$uid=str_replace('（ID为用户唯一不变标识）', '', $hasu);
				// dump($uid);
				$sdata['zhonguid']=$uid;
				$sdata['zj_status']=1;
				M('jiang')->where('id='.$jj['id'])->save($sdata);
			}
			$dom->clear();
		}
	}

	public function buildpageurl($arr){
		$str='http://1.163.com//detail/'.$arr['jiangid'].'-'.$arr['qiid'].'.html';
		return $str;
	}
    public function jiang(){
		$list=M('jiang')->where('quren_status=0 and zj_status=1')->order('id asc')->select();
		foreach($list as $jj){
			$url=$this->buildurl($jj, 1);
			// dump($url);
			$res=file_get_contents($url);
			$resarr=json_decode($res, true);
			// dump($resarr);
			if($resarr['code']==0){
				foreach($resarr['result']['list'] as $canjia){
					$this->addcanjia($canjia, $jj['id']);
				}
			}
			//处理其他页数
			$yeshu=ceil($resarr['result']['totalCnt']/50);
			// dump($yeshu);
			if($yeshu>=2){
				for($yy=2; $yy<=$yeshu;$yy++){
					$url=$this->buildurl($jj, $yy);
					// dump($url);
					$res=file_get_contents($url);
					$resarr=json_decode($res, true);
					// dump($resarr);
					if($resarr['code']==0){
						foreach($resarr['result']['list'] as $canjia){
							$this->addcanjia($canjia, $jj['id']);
						}
					}		
				}
			}
			$sdata['quren_status']=1;
			M('jiang')->where('id='.$jj['id'])->save($sdata);
		}
    }
    public function addcanjia($canjia, $jid){
    	$data['cishu']=$canjia['num'];
    	$data['jiangid']=$jid;
    	$data['uid']=$canjia['user']['cid'];
    	$data['rid']=$canjia['rid'];
    	M('canjia')->data($data)->add();


    	$ucc['uid']=$data['uid'];
    	$res=M('user')->where($ucc)->find();
    	if(!$res){
    		M('user')->data($ucc)->add();
    	}
    }
    public function buildurl($arr, $page){
    	//http://1.163.com/record/getDuobaoRecord.do?pageNum=1&pageSize=50&totalCnt=0&gid=1093&period=306050215
    	$str='http://1.163.com/record/getDuobaoRecord.do?pageNum='.$page.'&pageSize=50&totalCnt=0&gid='.$arr['jiangid'].'&period='.$arr['qiid'];
    	return $str;
    }

    public function gaoren(){
    	$list=M('user')->where('qucanjia_status=0')->order('id asc')->select();
    	foreach($list as $uu){
    		$url=$this->userurl($uu['uid'], 1);
    		$res=file_get_contents($url);
    		$resarr=json_decode($res, true);
    		// dump($resarr);

    		if($resarr['code']==0){
    			foreach($resarr['result']['list'] as $zj){
					$this->addzhongjiang($zj, $uu['uid']);
				}
    		}

    		$yeshu=ceil($resarr['result']['totalCnt']/10);
			// dump($yeshu);
			if($yeshu>=2){
				for($yy=2; $yy<=$yeshu;$yy++){
					$url=$this->userurl($uu['uid'], $yy);
					// dump($url);
					$res=file_get_contents($url);
					$resarr=json_decode($res, true);
					// dump($resarr);
					if($resarr['code']==0){
						foreach($resarr['result']['list'] as $zj){
							$this->addzhongjiang($zj, $uu['uid']);
						}
					}		
				}
			}
			$udata['qucanjia_status']=1;
			M('user')->where('id='.$uu['id'])->save($udata);
    	}
    }

    public function addzhongjiang($arr, $uid){
    	$cc['jiangid']=$arr['goods']['gid'];
    	$cc['qiid']=$arr['period'];
    	$res=M('jiang')->where($cc)->find();
    	if(!$res){
    		$cc['zhonguid']=$uid;
    		$cc['zj_status']=1;
    		M('jiang')->data($cc)->add();
    	}
    }

    public function userurl($uid, $page){
    	//http://1.163.com/user/win/get.do?pageNum=1&pageSize=12&totalCnt=0&cid=83261353&token=f0b0ea2c-ba1f-4b2b-be23-86992776c8f9&t=1465064265112
    	$str='http://1.163.com/user/win/get.do?pageNum='.$page.'&cid='.$uid;
    	return $str;
    }

  //   public function quhao(){
  //   	$list=M('jiang')->where('quhao_status=0')->order('id asc')->select();
  //   	foreach($list as $jj){
		// 	$rlist=M('canjia')->where('jiangid='.$jj['id'])->order('id desc')->select();
		// 	foreach($rlist as $rr){
		// 		$url=$this->quhaourl($rr, $jj);
		// 		dump($url);
		// 		$res=file_get_contents($url);
		// 		$resarr=json_decode($res, true);
		// 		dump($resarr);
		// 		die;
		// 	}

		// }
  //   }

  //   public function quhaourl($arr, $jarr){
  //   	//http://1.163.com/code/get.do?gid=1093&period=306050215&rid=110&token=33735ebc-363d-4855-b598-6050e6fcb563&t=1465062138196
  //   	$str='http://1.163.com/code/get.do?gid='.$jarr['jiangid'].'&period='.$jarr['qiid'].'&rid='.$arr['rid'];
  //   	return $str;
  //   }

    public function test(){
    	$res=file_get_contents('http://1.163.com//detail/1093-306050215.html');
    	dump($res);
    }
}











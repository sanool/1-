<?

class taobaodata{
	
	public $shopurl = '';

	public $ch='';

	public $dom='';

	public $filesave=0;
	function __construct($url){
		$this->filesave=1;
		Vendor('simple_html_dom');

		$this->shopurl=$url;
		$this->ch=curl_init();


        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,1);
        
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1); // 防止302 盗链
	}

	function __destruct()
    {

    }	

    function test(){
    	// $url='https://item.taobao.com/item.htm?spm=5148.7631246.0.0.AeTb7d&id=12374524448';
    	// $url='https://item.taobao.com/item.htm?spm=5148.7631246.0.0.AeTb7d&id=525000773545';
    	$url='https://detail.tmall.com/item.htm?spm=a230r.1.14.3.bM1yqW&id=37062437563&ns=1&abbucket=18';
    	return $this->item_rukou($url);
    }


    public function item_rukou ($url) {
        $urlname = parse_url($url);
        $urlname = $urlname['host'];
        switch ($urlname) {
            case 'item.taobao.com': //淘宝抓取专用
                $arr = $this->get_taobaoshop($url);
                break;
            case 'detail.tmall.com': //抓取天猫
                $arr = $this->get_tmallshop($url);
                break;
            default:
                $arr['error']=400;  
                break;
        }
		return $arr;
    }

    function get_relationkey(){

    }

    function search_item(){

    }

    function get_comments(){

    }

    function get_taobaoshop($url){
    	$content = $this->getcc($url);

    	// dump($content);
    	$this->dom=file_get_sal($content);
    	if($this->dom){
	    	$nod=$this->dom->find('.tb-main-title', 0);  //商品标题

	    	$data['title']=str_replace(" ","",strip_tags($nod->innertext));

	    	return $data;
    	}else{
    		return $data['error']='404'; //文件读取失
    	}
    }

    function get_tmallshop($url){
    	$content = $this->getcc($url);

    	// dump($content);die;
    	preg_match('/TShop.Setup\((.*?)\)\;/is', $content, $jsonall);
    	// dump($jsonall);
    	$jsondata=trim(str_replace(array("\r","\n"),"", $jsonall[1]));
    	// dump($jsondata);
    	$jsondata=iconv('GBK', 'UTF-8//IGNORE', $jsondata);
    	$jsonarr=json_decode($jsondata,true);
    	// dump($jsonarr);
    	// dump(json_last_error());
    	
    	//https://mdskip.taobao.com/core/initItemDetail.htm?isRegionLevel=true&isApparel=false&tryBeforeBuy=false&cachedTimestamp=1459568113329&itemId=37062437563&service3C=true&isSecKill=false&queryMemberRight=true&showShopProm=false&isAreaSell=false&isUseInventoryCenter=true&household=false&progressiveSupport=true&sellerPreview=false&addressLevel=3&offlineShop=false&cartEnable=true&isForbidBuyItem=false&tmallBuySupport=true&callback=setMdskip&timestamp=1459604265943&isg=Ah8fKuReX5A4f7VetW99Ac5Zj32oi3Mv&ref=https%3A%2F%2Fs.taobao.com%2Fsearch%3Fspm%3Da230r.1.0.0.k5rAKf%26q%3D%25E7%25A9%25BA%25E6%25B0%2594%25E5%2587%2580%25E5%258C%2596%25E5%2599%25A8%26spu_title%3D%25E9%25A3%259E%25E5%2588%25A9%25E6%25B5%25A6%2BAC4374%26app%3Ddetailproduct%26pspuid%3D639242%26cat%3D50018959%26from_pos%3D20_50018959.default_0_5_639242%26spu_style%3Dgrid
    	//实际价格见上面url  无法直接读取
        //参考 http://blog.csdn.net/phenixsoul/article/details/10052059

    	die;

        //下面的可能用不到
    	// $this->dom=file_get_sal($content);
    	// if($this->dom){
	    // 	$nod=$this->dom->find('.tb-detail-hd h1 a', 0);  //商品标题

	    // 	$data['title']=str_replace(" ","",strip_tags($nod->innertext));

	    // 	return $data;
    	// }else{
    	// 	return $data['error']='404'; //文件读取失败
    	// }
    }







    public function getcc($url){
    	$urlmd5=md5($url);
    	$localfile='./temp/taobao/'.$urlmd5;
    	if($this->filesave && file_exists($localfile)){
    		$cc=file_get_contents($localfile);
    		return $cc;
    	}else{
	    	curl_setopt($this->ch,CURLOPT_URL,$url);
	        $output = curl_exec($this->ch);
	        curl_close($this->ch);
	        if($output){
	        	if($this->filesave){
	        		file_put_contents($localfile, $output);
	        	}
	        	return $output;
	        }else{
	        	dump(curl_error($this->ch));
	        }
        }
    }
}


?>
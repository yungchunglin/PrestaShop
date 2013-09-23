<?php 
class cmshomepage extends Module {
	function __construct(){
		$this->name = 'cmshomepage';
		$this->tab = 'front_office_features';
        $this->author = 'MyPresta.eu';
		$this->version = '1.1.9';
        $this->dir = '/modules/cmshomepage/';
		parent::__construct();
		$this->displayName = $this->l('CMS on homepage');
		$this->description = $this->l('Insert CMS content to your homepage');
        
	}
    
	public function psversion() {
		$version=_PS_VERSION_;
		$exp=$explode=explode(".",$version);
		return $exp[1];
	}
        
	function install(){
        if (parent::install() == false 
	    OR $this->registerHook('home') == false
        OR Configuration::updateValue('cmshomepage', '0') == false
        ){
            return false;
        }
        return true;
	}
    
	public function getContent(){
	   	$output="";
		if (Tools::isSubmit('module_settings')){            		
			Configuration::updateValue('cmshomepage', $_POST['cmshomepage']);                                   
        }	   
        $output.="";
        return $output.$this->displayForm();
	}
    
    public function getCMS($lang){
    	return CMS::listCms($lang);
    }
    

	public function displayForm(){
	    $options="<option>".$this->l('-- SELECT --')."</option>";
	    $idlang = (int)Configuration::get('PS_LANG_DEFAULT');
        foreach (self::getCMS($idlang) AS $k=>$v){
            if (Configuration::get('cmshomepage')==$v['id_cms']){
                $selected='selected="yes"';
            } else {
                $selected='';
            }
            $options.="<option value=\"".$v['id_cms']."\" $selected>".$v['meta_title']."</option>";
        }
		$form='';
		return $form.'		
		<div style="diplay:block; clear:both; margin-bottom:20px;">
		<iframe src="http://mypresta.eu/content/uploads/2012/09/htmlbox_advertise.html" width="100%" height="130" border="0" style="border:none;"></iframe>
		</div>
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            <fieldset style="position:relative; margin-bottom:10px;">
            <legend>'.$this->l('Select CMS page').'</legend>
            <div style="display:block; margin:auto; overflow:hidden; width:100%; vertical-align:top;">
                <label>'.$this->l('CMS Page').':</label>
                    <div class="margin-form" style="text-align:left;" >
                    <select name="cmshomepage">'.$options.'
                    </select>
                </div>
                                          
                <div style="margin-top:20px; clear:both; overflow:hidden; display:block; text-align:center">
	               <input type="submit" name="module_settings" class="button" value="'.$this->l('save').'">
	            </div>
            </div>
            </fieldset>
		</form>';
	}   
   
	function hookhome($params){
	    if ($this->psversion()==4 || $this->psversion()==3){
            global $cookie;
            $this->context = new StdClass();
            $this->context->cookie=$cookie;
        }
        global $smarty;
        $smarty->assign('cms', new CMS(Configuration::get('cmshomepage'), $this->context->cookie->id_lang));
        return ($this->display(__FILE__, '/cmshomepage.tpl'));
	}
}
?>
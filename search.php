<?php
include_once("/templatepower/class.TemplatePower.inc.php");

$tpl = new TemplatePower("/html/in-search-list.html");
$tpl->assignInclude("rwd_header","/html/rwd_header.html");
$tpl->assignInclude("rwd_footer","/html/rwd_footer.html");
$tpl->assignInclude("seo_css_js","/html/seo_css_js.html");
$tpl->prepare();

$class_type       = $_GET["class_type"];
$mode_search      = $_GET["mode"];         //Search mode
$search_cat_id    = $_GET["cat_id"];
$search_parent_id = $_GET["parent_id"];
$search_goods = trim(urldecode($_GET["search_goods"]));
$time_start = microtime(true);             //start time


/********************************************************
 *
 * quick selection
 *
 *********************************************************/
if($search_cat_id != "")
{
	$select_show_cat_fast_sql = "select category_fast from pro.pro_category where cat_id='".$search_cat_id."'";
	$select_show_cat_fast_row = mysql_query($select_show_cat_fast_sql);

}
if($search_parent_id != "")
{
	$select_show_cat_fast_sql = "select category_fast from pro.pro_category where cat_id='".$search_parent_id."'";
	$select_show_cat_fast_row = mysql_query($select_show_cat_fast_sql);

}
if($search_cat_id =="" and $search_parent_id =="")
{
	$select_show_cat_fast_sql = "SELECT category_fast FROM pro.pro_category WHERE category_fast LIKE  '%$search_goods%' LIMIT 0 , 1";
	$select_show_cat_fast_row = mysql_query($select_show_cat_fast_sql);
}
while($select_show_cat_fast = mysql_fetch_assoc($select_show_cat_fast_row))
{
	$category_fast_field = $select_show_cat_fast["category_fast"];

	if($category_fast_field != "")
	{
		$cat_info = $category_fast_field;

		$cat_array = explode(",",$cat_info);

		if(count($cat_array) > 0 )
		{
			foreach($cat_array as $index => $value)
			{
				$tpl->newBlock("show_search_fast_tag");
				$tpl->assign("search_fast_name",$value);

				if($cat_id != "")
				{
					$tpl->assign("search_fast_url","/search.php?class_type=desc&search_goods=".$value);
				}
				if($sub_cat_id != "")
				{
					$tpl->assign("search_fast_url","/search.php?class_type=desc&&search_goods=".$value);
				}	
			}
		}
	}
}
$tpl->gotoBlock("_ROOT");
/********************************************************
 *
 * save input text
 *
 *********************************************************/
$tpl->assign("search_goods",$search_goods);
/********************************************************
 *
 * Other products
 *
 *********************************************************/
if($search_cat_id != "")
{
	$select_show_cat_keywords_sql = "select keywords from pro.pro_category where cat_id='".$search_cat_id."'";
	$select_show_cat_keywords_row = mysql_query($select_show_cat_keywords_sql);

}
if($search_parent_id != "")
{
	$select_show_cat_keywords_sql = "select keywords from pro.pro_category where cat_id='".$search_parent_id."'";
	$select_show_cat_keywords_row = mysql_query($select_show_cat_keywords_sql);

}
if($search_cat_id =="" and $search_parent_id =="")
{
	$select_show_cat_keywords_sql = "SELECT keywords FROM pro.pro_category WHERE keywords LIKE  '%$search_goods%' LIMIT 0 , 1";
	$select_show_cat_keywords_row = mysql_query($select_show_cat_keywords_sql);
}

while($select_show_cat_keywords = mysql_fetch_assoc($select_show_cat_keywords_row))
{
	$category_keywords_field = $select_show_cat_keywords["keywords"];

	if($category_keywords_field != "")
	{
		$cat_info = $category_keywords_field;

		$cat_array = explode(",",$cat_info);

		if(count($cat_array) > 0 )
		{
			foreach($cat_array as $index => $value)
			{
				$tpl->newBlock("show_keywords_tag");
				$tpl->assign("keywords_name",$value);

				if($cat_id != "")
				{
					$tpl->assign("keywords_url","/search.php?class_type=desc&search_goods=".$value);
				}
				if($sub_cat_id != "")
				{
					$tpl->assign("keywords_url","/search.php?class_type=desc&search_goods=".$value);
				}	
			}
		}
	}
}
$tpl->gotoBlock("_ROOT");
/********************************************************
 *
 * Related Categories quickly select , Other products ( total number )
 *
 *********************************************************/
//ALL(ALL TOTAL)
$goods_all_quantity_sql = "SELECT count(*) FROM pro.pro_goods WHERE is_on_sale =1 and (goods_sn like '%$search_goods%' or goods_name like '%$search_goods%' or goods_desc like '%$search_goods%')";
$goods_all_quantity_rows = mysql_query($goods_all_quantity_sql);
$goods_all_quantity = mysql_fetch_assoc($goods_all_quantity_rows);
$all_quantity_total = $goods_all_quantity['count(*)'];
$tpl->newBlock("gift_mainquantity");
$tpl->assign("goods_all_quantity",$all_quantity_total);

//GIFT(ALL TOTAL)
$goods_gift_quantity_sql = "SELECT g.goods_id, g.goods_name, COUNT( g.goods_id ) AS counts FROM pro.pro_goods AS g JOIN pro.pro_category AS c ON g.cat_id = c.cat_id WHERE c.shop_rule =1 AND g.is_on_sale =1 and (g.goods_sn like '%$search_goods%' or g.goods_name like '%$search_goods%' or g.goods_desc like '%$search_goods%')";
$goods_gift_quantity_rows = mysql_query($goods_gift_quantity_sql);
$goods_gift_quantity = mysql_fetch_assoc($goods_gift_quantity_rows);
$gift_quantity_total = $goods_gift_quantity['counts'];
if($gift_quantity_total != 0)
{
	$tpl->newBlock("goods_gift_quantity");
}


//SCHOOL(ALL TOTAL)
$goods_school_quantity_sql = "SELECT g.goods_id, g.goods_name, COUNT( g.goods_id ) AS counts FROM pro.pro_goods AS g JOIN pro.pro_category AS c ON g.cat_id = c.cat_id WHERE c.shop_rule =2 AND g.is_on_sale =1 and (g.goods_sn like '%$search_goods%' or g.goods_name like '%$search_goods%' or g.goods_desc like '%$search_goods%')";
$goods_school_quantity_rows = mysql_query($goods_school_quantity_sql);
$goods_school_quantity = mysql_fetch_assoc($goods_school_quantity_rows);
$school_quantity_total = $goods_school_quantity['counts'];
if($school_quantity_total != 0)
{
	$tpl->newBlock("goods_school_quantity");
}

//PRINT(ALL TOTAL)
$goods_print_quantity_sql = "SELECT g.goods_id, g.goods_name, COUNT( g.goods_id ) AS counts FROM pro.pro_goods AS g JOIN pro.pro_category AS c ON g.cat_id = c.cat_id WHERE c.shop_rule =0 AND g.is_on_sale =1 and (g.goods_sn like '%$search_goods%' or g.goods_name like '%$search_goods%' or g.goods_desc like '%$search_goods%')";
$goods_print_quantity_rows = mysql_query($goods_print_quantity_sql);
$goods_print_quantity = mysql_fetch_assoc($goods_print_quantity_rows);
$print_quantity_total = $goods_print_quantity['counts'];
if($print_quantity_total != 0){
	$tpl->newBlock("goods_print_quantity");
}

/********************************************************
 *
 * Related Categories quickly select , Other products ( category number )
 *
 *********************************************************/
//GIFT
$select_gift_quantity_sql = "SELECT DISTINCT cat_id,parent_id,cat_name  FROM pro.pro_category WHERE is_show ='1' AND shop_rule =  '1' AND parent_id =  '0' AND  category_link = ''  ORDER BY sort_order ASC";
$select_gift_quantity_rows = mysql_query($select_gift_quantity_sql);
while($select_gift_quantity = mysql_fetch_assoc($select_gift_quantity_rows))
{

	$gifttotal = 0;
	$gift_subtotal = 0;
	
	$cat_id = $select_gift_quantity['cat_id'];
	$cat_name = $select_gift_quantity['cat_name'];	

	//The total number of category
	$select_gift_subsub_quantity_sql = "SELECT * from pro.pro_category where is_show='1' and parent_id='$cat_id'  order by sort_order asc";
	$select_gift_subsub_quantity_row = mysql_query($select_gift_subsub_quantity_sql);
	while($select_gift_subsub_quantity = mysql_fetch_assoc($select_gift_subsub_quantity_row))
	{
		$subsub_cat_id    = $select_gift_subsub_quantity['cat_id'];
		$subsub_parent_id = $select_gift_subsub_quantity['parent_id'];

		//goods count
		$select_gift_count_sql = "SELECT g.cat_id, gc.cat_id, COUNT( * ) AS goods_num FROM pro.pro_goods AS g LEFT JOIN pro.pro_goods_cat AS gc ON g.cat_id = gc.cat_id WHERE is_delete =0 AND is_on_sale =1 AND g.cat_id = $subsub_cat_id and (g.goods_sn like '%$search_goods%' or g.goods_name like '%$search_goods%' or g.goods_desc like '%$search_goods%') GROUP BY g.cat_id";
		$select_gift_count_row = mysql_query($select_gift_count_sql);
		$select_gift_count = mysql_fetch_assoc($select_gift_count_row);
		$gift_goods_num = $select_gift_count['goods_num'];

		$gifttotal += $gift_goods_num;

	}
	
	if($gifttotal != 0)
	{
		$tpl->newBlock("gift_main");
		$tpl->newBlock("gift_main_string");
		$tpl->assign("gift_main_name",$cat_name); //category name
		$tpl->assign("gift_main_quantity","(".$gifttotal.")"); //category count
		$tpl->assign("gift_main_url","cat_id=$sub_cat_id&parent_id=$sub_parent_id&class_type=desc&search_goods=$search_goods");
		$select_gift_sub_quantity_sql = "SELECT * from pro.pro_category where is_show='1' and parent_id='$cat_id'  order by sort_order asc";
		$select_gift_sub_quantity_row = mysql_query($select_gift_sub_quantity_sql);
		while($select_gift_sub_quantity = mysql_fetch_assoc($select_gift_sub_quantity_row))
		{
			$sub_cat_id    = $select_gift_sub_quantity['cat_id'];
			$sub_parent_id = $select_gift_sub_quantity['parent_id'];
			$sub_cat_name  = $select_gift_sub_quantity['cat_name'];
			//goods count
			$select_gift_count_sql = "SELECT g.cat_id, gc.cat_id, COUNT( * ) AS goods_num FROM pro.pro_goods AS g LEFT JOIN pro.pro_goods_cat AS gc ON g.cat_id = gc.cat_id WHERE is_delete =0 AND is_on_sale =1 AND g.cat_id = $sub_cat_id and (g.goods_sn like '%$search_goods%' or g.goods_name like '%$search_goods%' or g.goods_desc like '%$search_goods%') GROUP BY g.cat_id";
			$select_gift_count_row = mysql_query($select_gift_count_sql);
			$select_gift_count = mysql_fetch_assoc($select_gift_count_row);

			$gift_goods_num = $select_gift_count['goods_num'];

			if($gift_goods_num != 0 ){
				$tpl->newBlock("gift_submain");
				$tpl->assign("search_goods",$search_goods);
				$tpl->assign("gift_submain_name",$sub_cat_name);//sub category name
				$tpl->assign("gift_submain_quantity","(".$gift_goods_num.")");//sub category count
				$tpl->assign("gift_submain_url","cat_id=$sub_cat_id&parent_id=$sub_parent_id&class_type=desc&search_goods=$search_goods");
			}
		}
	}
}

// SCHOOL
$select_school_quantity_sql = "SELECT DISTINCT cat_id,parent_id,cat_name  FROM pro.pro_category WHERE is_show ='1' AND shop_rule =  '2' AND parent_id =  '0' AND  category_link = ''  ORDER BY sort_order ASC";
$select_school_quantity_rows = mysql_query($select_school_quantity_sql);
while($select_school_quantity = mysql_fetch_assoc($select_school_quantity_rows))
{

	$schooltotal = 0;
	$school_subtotal = 0;
	
	$cat_id = $select_school_quantity['cat_id'];
	$cat_name = $select_school_quantity['cat_name'];	

	//The total number of category
	$select_school_subsub_quantity_sql = "SELECT * from pro.pro_category where is_show='1' and parent_id='$cat_id'  order by sort_order asc";
	$select_school_subsub_quantity_row = mysql_query($select_school_subsub_quantity_sql);
	while($select_school_subsub_quantity = mysql_fetch_assoc($select_school_subsub_quantity_row))
	{
		$subsub_cat_id    = $select_school_subsub_quantity['cat_id'];
		$subsub_parent_id = $select_school_subsub_quantity['parent_id'];

		//goods count
		$select_school_count_sql = "SELECT g.cat_id, gc.cat_id, COUNT( * ) AS goods_num FROM pro.pro_goods AS g LEFT JOIN pro.pro_goods_cat AS gc ON g.cat_id = gc.cat_id WHERE is_delete =0 AND is_on_sale =1 AND g.cat_id = $subsub_cat_id and (g.goods_sn like '%$search_goods%' or g.goods_name like '%$search_goods%' or g.goods_desc like '%$search_goods%') GROUP BY g.cat_id";
		$select_school_count_row = mysql_query($select_school_count_sql);
		$select_school_count = mysql_fetch_assoc($select_school_count_row);
		$school_goods_num = $select_school_count['goods_num'];

		$schooltotal += $school_goods_num;

	}
	
	if($schooltotal != 0)
	{
		$tpl->newBlock("school_main");
		$tpl->newBlock("school_main_string");
		$tpl->assign("school_main_name",$cat_name); //category name
		$tpl->assign("school_main_quantity","(".$schooltotal.")"); //category count
		$tpl->assign("school_main_url","cat_id=$sub_cat_id&parent_id=$sub_parent_id&class_type=desc&search_goods=$search_goods");
		$select_school_sub_quantity_sql = "SELECT * from pro.pro_category where is_show='1' and parent_id='$cat_id'  order by sort_order asc";
		$select_school_sub_quantity_row = mysql_query($select_school_sub_quantity_sql);
		while($select_school_sub_quantity = mysql_fetch_assoc($select_school_sub_quantity_row))
		{
			$sub_cat_id    = $select_school_sub_quantity['cat_id'];
			$sub_parent_id = $select_school_sub_quantity['parent_id'];
			$sub_cat_name  = $select_school_sub_quantity['cat_name'];
			//goods count
			$select_school_count_sql = "SELECT g.cat_id, gc.cat_id, COUNT( * ) AS goods_num FROM pro.pro_goods AS g LEFT JOIN pro.pro_goods_cat AS gc ON g.cat_id = gc.cat_id WHERE is_delete =0 AND is_on_sale =1 AND g.cat_id = $sub_cat_id and (g.goods_sn like '%$search_goods%' or g.goods_name like '%$search_goods%' or g.goods_desc like '%$search_goods%') GROUP BY g.cat_id";
			$select_school_count_row = mysql_query($select_school_count_sql);
			$select_school_count = mysql_fetch_assoc($select_school_count_row);

			$school_goods_num = $select_school_count['goods_num'];

			if($school_goods_num != 0 ){
				$tpl->newBlock("school_submain");
				$tpl->assign("search_goods",$search_goods);
				$tpl->assign("school_submain_name",$sub_cat_name);//sub category name
				$tpl->assign("school_submain_quantity","(".$school_goods_num.")");// sub category count
				$tpl->assign("school_submain_url","cat_id=$sub_cat_id&parent_id=$sub_parent_id&class_type=desc&search_goods=$search_goods");
			}
		}
	}
}
// PRINT
$select_print_quantity_sql = "SELECT DISTINCT cat_id,parent_id,cat_name  FROM pro.pro_category WHERE is_show ='1' AND shop_rule =  '0' AND parent_id =  '0' AND  category_link = ''  ORDER BY sort_order ASC";
$select_print_quantity_rows = mysql_query($select_print_quantity_sql);
while($select_print_quantity = mysql_fetch_assoc($select_print_quantity_rows))
{

	$printtotal = 0;
	$print_subtotal = 0;
	
	$cat_id = $select_print_quantity['cat_id'];
	$cat_name = $select_print_quantity['cat_name'];	

	//category count
	$select_print_subsub_quantity_sql = "SELECT * from pro.pro_category where is_show='1' and parent_id='$cat_id'  order by sort_order asc";
	$select_print_subsub_quantity_row = mysql_query($select_print_subsub_quantity_sql);
	while($select_print_subsub_quantity = mysql_fetch_assoc($select_print_subsub_quantity_row))
	{
		$subsub_cat_id    = $select_print_subsub_quantity['cat_id'];
		$subsub_parent_id = $select_print_subsub_quantity['parent_id'];

		//goods count
		$select_print_count_sql = "SELECT g.cat_id, gc.cat_id, COUNT( * ) AS goods_num FROM pro.pro_goods AS g LEFT JOIN pro.pro_goods_cat AS gc ON g.cat_id = gc.cat_id WHERE is_delete =0 AND is_on_sale =1 AND g.cat_id = $subsub_cat_id and (g.goods_sn like '%$search_goods%' or g.goods_name like '%$search_goods%' or g.goods_desc like '%$search_goods%') GROUP BY g.cat_id";
		$select_print_count_row = mysql_query($select_print_count_sql);
		$select_print_count = mysql_fetch_assoc($select_print_count_row);
		$print_goods_num = $select_print_count['goods_num'];

		$printtotal += $print_goods_num;

	}
	
	if($printtotal != 0)
	{
		$tpl->newBlock("print_main");
		$tpl->newBlock("print_main_string");
		$tpl->assign("print_main_name",$cat_name); //calss name
		$tpl->assign("print_main_quantity","(".$printtotal.")"); //class count
		$tpl->assign("print_main_url","cat_id=$sub_cat_id&parent_id=$sub_parent_id&class_type=desc&search_goods=$search_goods");
		$select_print_sub_quantity_sql = "SELECT * from pro.pro_category where is_show='1' and parent_id='$cat_id'  order by sort_order asc";
		$select_print_sub_quantity_row = mysql_query($select_print_sub_quantity_sql);
		while($select_print_sub_quantity = mysql_fetch_assoc($select_print_sub_quantity_row))
		{
			$sub_cat_id    = $select_print_sub_quantity['cat_id'];
			$sub_parent_id = $select_print_sub_quantity['parent_id'];
			$sub_cat_name  = $select_print_sub_quantity['cat_name'];
			//goods count
			$select_print_count_sql = "SELECT g.cat_id, gc.cat_id, COUNT( * ) AS goods_num FROM pro.pro_goods AS g LEFT JOIN pro.pro_goods_cat AS gc ON g.cat_id = gc.cat_id WHERE is_delete =0 AND is_on_sale =1 AND g.cat_id = $sub_cat_id and (g.goods_sn like '%$search_goods%' or g.goods_name like '%$search_goods%' or g.goods_desc like '%$search_goods%') GROUP BY g.cat_id";
			$select_print_count_row = mysql_query($select_print_count_sql);
			$select_print_count = mysql_fetch_assoc($select_print_count_row);

			$print_goods_num = $select_print_count['goods_num'];

			if($print_goods_num != 0 ){
				$tpl->newBlock("print_submain");
				$tpl->assign("search_goods",$search_goods);
				$tpl->assign("print_submain_name",$sub_cat_name);//子類名稱
				$tpl->assign("print_submain_quantity","(".$print_goods_num.")");//子類數量
				$tpl->assign("print_submain_url","cat_id=$sub_cat_id&parent_id=$sub_parent_id&class_type=desc");
			}
		}
	}
}
$tpl->gotoBlock("_ROOT");
/********************************************************
 *
 * goods click show
 *
 *********************************************************/
$temp_goods_id = array();
$temp_goods_id_index = 0;
if($class_type == "desc" )
{	
	//hide page
	$tpl->assign("page_display","style=\"display:none;\"");

	$select_pro_main_goods_sql = "SELECT * FROM pro.pro_goods where cat_id='$search_cat_id' and is_on_sale='1'";
	$select_pro_main_goods_rows = mysql_query($select_pro_main_goods_sql);
	while($select_pro_main_goods = mysql_fetch_assoc($select_pro_main_goods_rows))
	{
		$goods_id = $select_pro_main_goods["goods_id"];
		$select_pro_goods_sql = "SELECT * from pro.pro_goods where goods_id='$goods_id' and is_on_sale='1'";
		$select_pro_goods_rows = mysql_query($select_pro_goods_sql);
		$select_pro_goods = mysql_fetch_assoc($select_pro_goods_rows);
		if($select_pro_goods["is_on_sale"] == 1)
		{
			$temp_goods_id[$temp_goods_id_index] = $select_pro_goods["goods_id"];
			$temp_goods_id_index++;
		}
	}

	if($search_cat_id != "")
	{
		$select_pro_goods_cat_sql = "select * from pro.pro_goods_cat where cat_id='$search_cat_id'";
		$select_pro_goods_cat_rows = mysql_query($select_pro_goods_cat_sql);
		while($select_pro_goods_cat = mysql_fetch_assoc($select_pro_goods_cat_rows))
		{
			$goods_id = $select_pro_goods_cat["goods_id"];
			$select_pro_goods_sql = "select * from pro.pro_goods where goods_id='$goods_id' and is_on_sale='1'";
			$select_pro_goods_rows = mysql_query($select_pro_goods_sql);
			$select_pro_goods = mysql_fetch_assoc($select_pro_goods_rows);
			if($select_pro_goods["is_on_sale"] == 1)
			{
				$temp_goods_id[$temp_goods_id_index] = $select_pro_goods["goods_id"];
				$temp_goods_id_index++;
			}
		}
	}
	else
	{
		$select_pro_category_sql = "select * from pro.pro_category where parent_id='$search_parent_id' and is_show='1' order by cat_id asc";
		$select_pro_category_rows = mysql_query($select_pro_category_sql);
		while($select_pro_category = mysql_fetch_assoc($select_pro_category_rows))
		{
			$cat_id = $select_pro_category["cat_id"];
			$select_pro_goods_cat_sql = "select * from pro.pro_goods_cat where cat_id='$cat_id'";

			$select_pro_goods_cat_rows = mysql_query($select_pro_goods_cat_sql);
			while($select_pro_goods_cat = mysql_fetch_assoc($select_pro_goods_cat_rows))
			{
				$goods_id = $select_pro_goods_cat["goods_id"];
				
				$select_pro_goods_sql = "select * from pro.pro_goods where goods_id='$goods_id' and is_on_sale='1'";
				$select_pro_goods_rows = mysql_query($select_pro_goods_sql);
				$select_pro_goods = mysql_fetch_assoc($select_pro_goods_rows);
				if($select_pro_goods["is_on_sale"] == 1)
				{
					$temp_goods_id[$temp_goods_id_index] = $select_pro_goods["goods_id"];
					$temp_goods_id_index++;
				}
			}
		}
	}

	$total_records = sizeof($temp_goods_id); //all count
	rsort($temp_goods_id);
	for($i=0; $i<$temp_goods_id_index; $i++)
	{	
		$tpl->newBlock("search_goods");
		
		$goods_id = $temp_goods_id[$i];
		$select_pro_goods_sql = "select * from pro.pro_goods where goods_id='".$goods_id."' and is_on_sale='1' ";
		$select_pro_goods_rows = mysql_query($select_pro_goods_sql);
		$select_pro_goods = mysql_fetch_assoc($select_pro_goods_rows);

		$select_pro_category_sql = "select cat_id,cat_name,parent_id,shop_rule from pro.pro_category where cat_id='".$select_pro_goods["cat_id"]."'";
		$select_pro_category_rows = mysql_query($select_pro_category_sql);
		$select_pro_category = mysql_fetch_assoc($select_pro_category_rows);

		$select_pro_category_sql2 = "select cat_id,cat_name,parent_id,shop_rule from pro.pro_category where cat_id='".$select_pro_category["parent_id"]."'";
		$select_pro_category_rows2 = mysql_query($select_pro_category_sql2);
		$select_pro_category2 = mysql_fetch_assoc($select_pro_category_rows2);
		
		$shop_type;
		$cat_id             = $select_pro_category["parent_id"];
		$cat_name           = $select_pro_category2["cat_name"];
		$eng_name 			= $select_pro_category_type["eng_name"];
		$sub_cat_id         = $select_pro_goods["cat_id"];
		$goods_id           = $select_pro_goods["goods_id"];
		$goods_sn           = $select_pro_goods["goods_sn"];
		$goods_name         = $select_pro_goods["goods_name"];
		$goods_thumb  = $select_pro_goods["goods_thumb"];
		$goods_original_img = $select_pro_goods["original_img"];
		$goods_goods_img    = $select_pro_goods["goods_img"];
		$in_shop_price      = $select_pro_goods["shop_price"];
		$in_shop_unit       = $select_pro_goods["shop_unit"];
		$goods_desc         = $select_pro_goods["goods_desc"];
		$static_rule        = $select_pro_category["static_rule"];
		/***************************************************
		 *
		 * price display
		 *
		 ***************************************************/	
		$in_shop_price = $select_pro_goods["shop_price"];
		$in_shop_unit = $select_pro_goods["shop_unit"];
		if($in_shop_price <= 0 or $in_shop_unit <= 0)
		{				
			$tpl->assign("show_shop_price","<font style=\"font: bold 12px Verdana,Helvetica,Arial;\" color=\"#79110e\"><b>Welcome to inquire the price</b></font>");
		}
		else
		{
			$temp_shop_price = $in_shop_price;
			$tpl->assign("show_shop_price","<a href=\"$to_url\" title=\"$temp_shop_price\"><font style=\"font: bold 15px/1.2 Verdana,Helvetica,Arial;\" color=\"#ff007c\"><b>$".$in_shop_price."</b></font>&nbsp;&nbsp;<font style=\"font: 15px Verdana,Helvetica,Arial;\"  color=\"#79110e\"><b>amount&nbsp;".$in_shop_unit."</b></font></a>");	
			$inquiry_flag = 1;
		}		
		/***************************************************
		 *
		 * get the main category and sub category info
		 *
		 ***************************************************/
		if($select_pro_category["parent_id"] == 0)
		{
			$cat_id = $sub_cat_id;
		}
		else
		{
			$cat_id = $select_pro_category["parent_id"];
		}
		

		if($cat_id == 0){
			$select_pro_category_type_sql ="select * from pro.pro_category where cat_id=".$sub_cat_id;
			$select_pro_category_type_rows = mysql_query($select_pro_category_type_sql);
			$select_pro_category_type = mysql_fetch_assoc($select_pro_category_type_rows);
			$shop_type = $select_pro_category_type["shop_rule"];
			
		}else{
			$select_pro_category_type_sql ="select * from pro.pro_category where cat_id=".$cat_id;
			$select_pro_category_type_rows = mysql_query($select_pro_category_type_sql);
			$select_pro_category_type = mysql_fetch_assoc($select_pro_category_type_rows);
			$shop_type = $select_pro_category_type["shop_rule"];
		}
	

		$select_pro_goods_gallery_check_sql = "select * from pro.pro_goods_gallery where goods_id='$goods_id' order by img_id asc";
		$select_pro_goods_gallery_check_rows = mysql_query($select_pro_goods_gallery_check_sql);
		$select_pro_goods_gallery_check = mysql_fetch_assoc($select_pro_goods_gallery_check_rows);
		$goods_img_original = $select_pro_goods_gallery_check["img_original"];
		
		/***************************************************
		 *
		 * Determine whether there is image level
		 *
		 ***************************************************/
		if(is_file($goods_img_original))
		{
			$tpl->assign("goods_img", "/images/goods/$goods_id/0.jpg");
		}
		else if(is_file($goods_thumb ))
		{
			$tpl->assign("goods_img","/".$goods_thumb);
		}
		else if(is_file($goods_original_img ))
		{
			$tpl->assign("goods_img","/".$goods_original_img);	
		}			
		else if(is_file($goods_goods_img ))
		{
			$tpl->assign("goods_img","/".$goods_goods_img);
		}else{
			$tpl->assign("goods_img","/images/error.jpg");
		}
		
		

		/***************************************************
		 *
		 * If you have Search Results in line with the text turns red Num.		 
		 *
		 ***************************************************/
		if(preg_match("/$search_goods/i",$goods_sn))
		{
			$replace_goods_sn_str = str_replace("$search_goods","<font color=red>$search_goods</font>",$goods_sn);
		}
		else
		{
			$replace_goods_sn_str = $goods_sn;
		}

		if(preg_match("/$search_goods/i",$goods_name))
		{
			$replace_goods_name_str = str_replace("$search_goods","<font color=red>$search_goods</font>",$goods_name);
		}
		else
		{
			$replace_goods_name_str = $goods_name;
		}
		//echo $replace_goods_sn_str."<br />";
		$tpl->assign("goods_sn",$replace_goods_sn_str);
		$tpl->assign("goods_name",$replace_goods_name_str);
		/***************************************************
		 *
		 * shop 0 = cart，1 = Inquiry
		 *
		 ***************************************************/	
		if($shop_type == 0)
		{
			$tpl->assign("inquirt_btn","btn-addcart");
			$tpl->assign("inquiry1","<a class=\"add-cart\" href=\"/goods_show_detail.php?cat_id=$cat_id&sub_cat_id=$sub_cat_id&goods_id=$goods_id\" title=\"add cart\">add cart</a>");
		}
		else
		{
			$tpl->assign("inquirt_btn","btn-addinq");
			$tpl->assign("inquiry1","<a class=\"add-cart\" href=\"/query_price.php?cat_id=$cat_id&sub_cat_id=$sub_cat_id&goods_id=$goods_id\" title=\"add Inquiry\">add Inquiry</a>");
		}


		$tpl->assign("goods_name_alt",$goods_name);
		$tpl->assign("goods_desc",$goods_desc);
		$tpl->assign("goods_id",$goods_id);
		$tpl->assign("goods_sn",$goods_sn);
		$tpl->assign("cat_name",$cat_name);
		$tpl->assign("sub_cat_name",$cat_name);
		$tpl->assign("search_cat_id",$cat_id);
		$tpl->assign("search_sub_cat_id",$sub_cat_id);
		$tpl->assign("search_goods_id",$goods_id);
	}
}

$tpl->gotoBlock("_ROOT");
/********************************************************
 *
 * New Arrivals、Latest updates 
 *
 *********************************************************/
$get_cat_id = $_GET["cat_id"];
$get_sub_cat_id = $_GET["sub_cat_id"];
$find_goods = array();
$find_goods_index =0 ;
if($min_price != "" or $max_price != "")
{
	if($get_sub_cat_id != "")
	{
		//main category
		$select_pro_goods_sql = "select * from pro.pro_goods where cat_id='$get_sub_cat_id' and is_on_sale='1' and  shop_price BETWEEN $min_price and $max_price order by goods_id desc";
	}
	else if($get_cat_id != "")
	{
		//sub category
		$select_pro_goods_sql = "select * from pro.pro_goods where cat_id='$get_cat_id' and is_on_sale='1' and  shop_price BETWEEN $min_price and $max_price order by goods_id desc";
	}
	else
	{	
		//no choose
		$select_pro_goods_sql = "select * from pro.pro_goods where is_on_sale='1' and shop_price BETWEEN $min_price and $max_price order by goods_id desc";
	}
}
else if($mode_search !="")
{
	if($mode_search == "updated"){
		$select_pro_goods_sql = "SELECT * from pro.pro_goods where is_on_sale='1' ORDER by add_time DESC";
		$tpl->assign("search_name","new arrival");		
	}
	else if($mode_search == "added")
	{
		$select_pro_goods_sql = "SELECT * FROM pro.pro_goods WHERE is_on_sale ='1' ORDER BY last_update DESC ";
		$tpl->assign("search_name","latest update");
	}
	else
	{
		$select_pro_goods_sql = "select * from pro.pro_goods where is_on_sale='1' and (goods_sn like '%$search_goods%' or goods_name like '%$search_goods%' or goods_desc like '%$search_goods%')  order by goods_id desc";
		$tpl->assign("search_name","Search Results");
	}
}
else
{
	$select_pro_goods_sql = "select * from pro.pro_goods where is_on_sale='1' and (goods_sn like '%$search_goods%' or goods_name like '%$search_goods%' or goods_desc like '%$search_goods%')  order by goods_id desc";
	$tpl->assign("search_name","Search Results");
}


$temp_search_cat_id = array();
$temp_search_cat_id_index = 0;
$temp_search_cat_id_check = 0;
$select_pro_goods_rows = mysql_query($select_pro_goods_sql);
while($select_pro_goods = mysql_fetch_assoc($select_pro_goods_rows))
{
	//check cat_id ,Dont repeat
	if($temp_search_cat_id_check != $select_pro_goods["cat_id"])
	{
		$temp_search_cat_id[$temp_search_cat_id_index] = $select_pro_goods["cat_id"];	
		$temp_search_cat_id_check = $select_pro_goods["cat_id"];
		$temp_search_cat_id_index++;
	}
	
	$find_goods[$find_goods_index] =  $select_pro_goods["goods_id"];
	$find_goods_index++;
	
}

/********************************************************
 *
 * Shows category price range
 *
 *********************************************************/
$price_tr_index = -1;
//echo sizeof($temp_search_cat_id);
$temp_search_cat_id = array_unique($temp_search_cat_id);
if($min_price == "" and $max_price == "")
{
	//echo "有";
	$temp_search_cat_id = 0;
}

$tpl->gotoBlock("_ROOT");
/********************************************************
 *
 * class_type == all_goods
 *
 *********************************************************/
if($class_type == "all_goods")
{
	//Shows find items
	$replace_goods_sn_str = "";
	$replace_goods_name_str = "";
	$search_cat_id = "";
	$search_sub_cat_id = "";
	
	//page
	$total_records = sizeof($find_goods);
	$per_page_size = 36;
	$per_page_size_index = 1;
	$get_page = $_GET["now_page"];


	$page_j =$per_page_size*($get_page-1);
	if($get_page == "" or $get_page == 0)
	{
		$page_j=0;
		if($total_records == 0)
		{
			$get_page = 0;
		}
		else
		{
			$get_page = 1;
		}
	}
	
	$tpl->assign("now_page",$get_page);
	$tpl->assign("total_search",$total_records);
	$goods_show_index = 0;
	$goods_repeat_flag=0;//Check Repeat
	

	for($i=$page_j;$i<sizeof($find_goods);$i++)
	{
		if($per_page_size_index <= $per_page_size)
		{
			$select_pro_goods_sql = "select * from pro.pro_goods  where goods_id='".$find_goods[$i]."' and is_on_sale='1' ";
			$select_pro_goods_rows = mysql_query($select_pro_goods_sql);
			while($select_pro_goods = mysql_fetch_assoc($select_pro_goods_rows))
			{
				$select_pro_category_sql = "select cat_id,cat_name,parent_id,shop_rule from pro.pro_category where cat_id='".$select_pro_goods["cat_id"]."'";
				$select_pro_category_rows = mysql_query($select_pro_category_sql);
				$select_pro_category = mysql_fetch_assoc($select_pro_category_rows);
		
				$select_pro_category_sql2 = "select cat_id,cat_name,parent_id,shop_rule from pro.pro_category where cat_id='".$select_pro_category["parent_id"]."'";
				$select_pro_category_rows2 = mysql_query($select_pro_category_sql2);
				$select_pro_category2 = mysql_fetch_assoc($select_pro_category_rows2);
				
				$tpl->newBlock("search_goods");
				
				$goods_id = $select_pro_goods["goods_id"];
				if($select_pro_category["parent_id"] == 0)
				{
					$cat_id = $select_pro_goods["cat_id"];
				}
				else
				{
					$cat_id = $select_pro_category["parent_id"];
				}
				
				/***************************************************
				*
				*  shop_type = 0 (car)  , = 1 (Inquiry)  
				*
				***************************************************/	
				$shop_type;
				$cat_id             = $select_pro_category["parent_id"];
				$sub_cat_id         = $select_pro_goods["cat_id"];
				$cat_name           = $select_pro_category2["cat_name"];
				$eng_name 			= $select_pro_category_type["eng_name"];
				$goods_id           = $select_pro_goods["goods_id"];
				$goods_sn           = $select_pro_goods["goods_sn"];
				$goods_name         = $select_pro_goods["goods_name"];
				$goods_desc         = $select_pro_goods["goods_desc"];
				$goods_thumb  = $select_pro_goods["goods_thumb"];
				$goods_original_img = $select_pro_goods["original_img"];
				$goods_goods_img    = $select_pro_goods["goods_img"];
				$in_shop_price      = $select_pro_goods["shop_price"];
				$in_shop_unit       = $select_pro_goods["shop_unit"];
				$gift_mode          = $select_pro_goods["goods_gift_mode"];

				if($cat_id == 0){
					$select_pro_category_type_sql ="select * from pro.pro_category where cat_id=".$sub_cat_id;
					$select_pro_category_type_rows = mysql_query($select_pro_category_type_sql);
					$select_pro_category_type = mysql_fetch_assoc($select_pro_category_type_rows);
					$shop_type = $select_pro_category_type["shop_rule"];
					
				}else{
					$select_pro_category_type_sql ="select * from pro.pro_category where cat_id=".$cat_id;
					$select_pro_category_type_rows = mysql_query($select_pro_category_type_sql);
					$select_pro_category_type = mysql_fetch_assoc($select_pro_category_type_rows);
					$shop_type = $select_pro_category_type["shop_rule"];
				}

				$goods_id = $find_goods[$i];
				$select_pro_goods_gallery_check_sql = "select * from pro.pro_goods_gallery where goods_id='$goods_id' order by img_id asc";
				$select_pro_goods_gallery_check_rows = mysql_query($select_pro_goods_gallery_check_sql);
				$select_pro_goods_gallery_check = mysql_fetch_assoc($select_pro_goods_gallery_check_rows);
				$goods_img_original = $select_pro_goods_gallery_check["img_original"];
				
				/***************************************************
				 *
				 * Analyzing image if there is a hierarchy
				 *
				 ***************************************************/	
				if(is_file($goods_img_original))
				{
					$tpl->assign("goods_img", "/images/goods/$goods_id/0.jpg");
				}
				else if(is_file($select_pro_goods["goods_thumb"]))
				{
					$tpl->assign("goods_img","/".$goods_thumb);
				}
				else if(is_file($select_pro_goods["original_img"]))
				{
					$tpl->assign("goods_img","/".$goods_original_img);	
				}			
				else if(is_file($select_pro_goods["goods_img"]))
				{
					$tpl->assign("goods_img","/".$goods_goods_img);
				}else{
					$tpl->assign("goods_img","/images/error.jpg");
				}
				
				/***************************************************
				 *
				 * If  search results will be in line with the text turns red text.
				 *
				 ***************************************************/				
				if(preg_match("/$search_goods/i",$goods_sn))
				{
					$replace_goods_sn_str = str_replace("$search_goods","<font color=red>$search_goods</font>",$goods_sn);
					//echo $replace_goods_sn_str;
				}
				else
				{
					$replace_goods_sn_str = $goods_sn;
				}
		
				if(preg_match("/$search_goods/i",$goods_name))
				{
					$replace_goods_name_str = str_replace("$search_goods","<font color=red>$search_goods</font>",$goods_name);
				}
				else
				{
					$replace_goods_name_str = $goods_name;
				}
				
				$tpl->assign("goods_sn",$replace_goods_sn_str);
				$tpl->assign("goods_name",$replace_goods_name_str);
				/***************************************************
				 *
				 * shop_type
				 *
				 ***************************************************/	
				if($shop_type == 0)
				{
					$tpl->assign("inquirt_btn","btn-addcart");
					$tpl->assign("inquiry1","<a class=\"add-cart\" href=\"/goods_show_detail.php?cat_id=$cat_id&sub_cat_id=$sub_cat_id&goods_id=$goods_id\" title=\"add cart\">add cart</a>");
				}
				else
				{
					$tpl->assign("inquirt_btn","btn-addinq");
					$tpl->assign("inquiry1","<a class=\"add-cart\" href=\"/query_price.php?cat_id=$cat_id&sub_cat_id=$sub_cat_id&goods_id=$goods_id\" title=\"add Inquiry\">add Inquiry</a>");
				}


				/***************************************************
				 *
				 * price display
				 *
				 ***************************************************/	
				$in_shop_price = $select_pro_goods["shop_price"];
				$in_shop_unit = $select_pro_goods["shop_unit"];
				if($in_shop_price <= 0 or $in_shop_unit <= 0)
				{				
					$tpl->assign("show_shop_price","<font style=\"font: bold 12px Verdana,Helvetica,Arial;\" color=\"#79110e\"><b>add Inquiry</b></font>");
				}
				else
				{
					$temp_shop_price = $in_shop_price;
					$tpl->assign("show_shop_price","<a href=\"$to_url\" title=\"$temp_shop_price\"><font style=\"font: bold 15px/1.2 Verdana,Helvetica,Arial;\" color=\"#ff007c\"><b>$".$in_shop_price."</b></font>&nbsp;&nbsp;<font style=\"font: 15px Verdana,Helvetica,Arial;\"  color=\"#79110e\"><b>數量&nbsp;".$in_shop_unit."</b></font></a>");	
					$inquiry_flag = 1;
				}


				$tpl->assign("goods_name_alt",$goods_name);
				$tpl->assign("goods_desc",$goods_desc);	
				$tpl->assign("goods_id",$goods_id);
				$tpl->assign("goods_sn",$goods_sn);
				$tpl->assign("cat_name",$goods_name);
				$tpl->assign("sub_cat_name",$goods_name);
				$tpl->assign("search_cat_id",$cat_id);
				$tpl->assign("search_sub_cat_id",$sub_cat_id);
				$tpl->assign("search_goods_id",$goods_id);

			}
			$per_page_size_index++;	
			$goods_show_index++;
		}
	}
	
	
	$tpl->gotoBlock("_ROOT");
	/***************************************************
	*
	* page
	*
	***************************************************/	
	if($total_records != 0)
	{
		$search_goods = urlencode($_GET["search_goods"]);
		//echo $search_goods;
		$total_page = ceil($total_records/$per_page_size);
		$tpl->gotoBlock("_ROOT");
		$tpl->assign("search_total_pages",$total_page);
		//echo $total_page."<br />";
		$per_page_recorcs = $per_page_size;
		$per_page_pages = 20;
		$divider=0;
		if($total_page < $per_page_pages)
		{
			$per_page_pages = $total_page;
		}
		if($get_page % $per_page_pages == 0 )
		{
			//$divider = floor($get_page/$per_page_pages);
			if($get_page  == $per_page_pages)
			{
				$show_pages_j = $get_page  / $per_page_pages;
			}
			else
			{
				$show_pages_j = $get_page -9;
			}
		}
		else
		{
			$show_pages_j = floor($get_page/$per_page_pages)*($per_page_pages)+1;
		}
		$show_pages_index = 1;
		$pages_str2 = "";
		
		$previous_page = $get_page-1;
		$next_page = $get_page+1;
		if($get_page != 1 && $total_records > $per_page_recorcs)
		{
			$previous_value = $get_page-1;
			$tpl->gotoBlock("_ROOT");	
			if($search_goods != "")
			{
				$tpl->assign("first_page","/search.php?now_page=1&search_goods=$search_goods&class_type=all_goods");
				$tpl->assign("previous_page","/search.php?now_page=$previous_value&search_goods=$search_goods&class_type=all_goods");		
			}
			else
			{
				$tpl->assign("first_page","/search.php?now_page=1&search_goods=$search_goods&class_type=all_goods");
				$tpl->assign("previous_page","/search.php?now_page=$previous_value&search_goods=$search_goods&class_type=all_goods");
			}
		}
		
		for($j=$show_pages_j;$j<=$total_page;$j++)
		{
			if($show_pages_index <= ($per_page_pages ))
			{
				if($j == $get_page)
				{
					if($get_article_cat_id != "")
					{
						$pages_str2 .="<a href=/search.php?now_page=$j&search_goods=$search_goods&class_type=all_goods ><li class=\"ck\">$j</li></a>";	
					}
					else
					{
						$pages_str2 .="<a href=/search.php?now_page=$j&search_goods=$search_goods&class_type=all_goods ><li class=\"ck\">$j</li></a>";		
					}
				}
				else
				{
					if($get_article_cat_id != "")
					{
						$pages_str2 .="<a href=/search.php?now_page=$j&search_goods=$search_goods&class_type=all_goods><li>$j</li></a>";
					}
					else
					{
						$pages_str2 .="<a href=/search.php?now_page=$j&search_goods=$search_goods&class_type=all_goods><li>$j</li></a>";		
					}
				}			
				$show_pages_index++;
			}
		}
		$tpl->gotoBlock("_ROOT");	
		$tpl->assign("pages_str",$pages_str2);
		
		if($get_page != $total_page  && $total_records >$per_page_size)
		{
			$next_page = $get_page +1;
			$tpl->gotoBlock("_ROOT");	
			if($search_goods != "")
			{
				$tpl->assign("next_page","/search.php?now_page=$next_page&search_goods=$search_goods&class_type=all_goods");
			}
			else
			{
				$tpl->assign("next_page","/search.php?now_page=$next_page&search_goods=$search_goods&class_type=all_goods");
			}
		}
		
		if($get_page == $total_page  && $total_records >$per_page_size)
		{
			//$tpl->assign("last_page","<b><a href=\"/news2.php?now_page=$total_page\"><font color=\"#FFFF00\">&nbsp;最後一頁</font></a></b>");
		}
		
		if($get_page != $total_page  && $total_records >$per_page_size)
		{
			$tpl->gotoBlock("_ROOT");
			if($search_goods != "")
			{
				$tpl->assign("last_page","/search.php?now_page=$total_page&search_goods=$search_goods&class_type=all_goods");
			}
			else
			{
				$tpl->assign("last_page","/search.php?now_page=$total_page&class_type=all_goods");
			}
		}
	}

	/***************************************************
	*
	* Search time
	*
	***************************************************/
	$time_end = microtime(true);
	$total_time = $time_end - $time_start;
	$total_time = substr ("$total_time", 0, 5);;
	$tpl->assign("total_time",$total_time);
}

$tpl->gotoBlock("_ROOT");
$tpl->assign("goods_total",$total_records);
$tpl->printToScreen();
?>
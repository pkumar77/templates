<?
   require_once("config.php");
   if (isset($_GET['search']))
   	{
      $formset['cname'] = "Searching for: " . $_GET['search'];
   	$querystring = "select * from formula where title like '%" . $_GET['search'] . "%' order by title";
   	}
   else
   	{
	   $c = (isset($_GET['c'])) ? $_GET['c'] : "";
	   $f = (isset($_GET['f'])) ? $_GET['f'] : "";
	   $t = (isset($_GET['t'])) ? $_GET['t'] : "";
	   $catid = 0;
	   $linedata = array();
	   if (strlen($c))
	      {
	      $db->query("select * from category where curl='$c'");
	      if ($db->getRowCount())
	         $formset = $db->getRow();
	      $catid = $formset['id'];
	      if (!strlen($f) && !strlen($t))
	         $querystring = "select * from formula where category_id=$catid order by function_id, disp_order";
	      }
	   if (strlen($f))
	      {
	      $db->query("select * from function where function_name='$f'");
	      if ($db->getRowCount())
	         $paren = $db->getRow();
	      $funid = $paren['id'];
	      if ($catid)
	         $querystring = "select * from formula where category_id=$catid and function_id=$funid order by function_id, disp_order";
	      else
	         $querystring = "select * from formula where function_id=$funid order by disp_order";
	      $formset['cname'] .= " (" . $paren['fdisplay_name'] . ")";
	      }
	   else if (strlen($t))
	      {
	      if ($catid)
	         $querystring = "select * from formula where category_id=$catid and obj_type='$t' order by function_id, disp_order";
	      else
	         $querystring = "select * from formula where obj_type='$t' order by disp_order";
	      $formset['cname'] .= " ($t)";
	      }
	   else if (!strlen($c))
	      {
	      $formset['cname'] = "SAS Templates";
	      $querystring = "select * from formula order by title";
	      }
	   }
	if (strlen($querystring))
      {
      $db->query($querystring);
      if ($db->getRowCount())
         {
         while ($lin = $db->getRow())
         	{

         	if (wf_file_exists(SITE_FILES_DIR . $lin['scriptname']))
  			{
         		
				$wfdb->query("select id as tlfid from tlf where product='testproduct' and study='templatestudy' and project='csr' and progname='" . $lin['scriptname'] . "'");
			      if ($wfdb->getRowCount())
			      	{
						$wfid = $wfdb->getRow();
						$lin['tlfid'] = $wfid['tlfid'];
						$lin['status']  =  "";
						$wfdb->query("select status from rev where tlf_id=" . $lin['tlfid'] . " order by id desc limit 0,1");
				      if ($wfdb->getRowCount())
				      	{
							$wfstat = $wfdb->getRow();
							if (in_array($wfstat['status'], array("Passed-QC", "Review-Stats", "Passed-Stats"))
							|| in_array($_SESSION['author_id'], array(1,2,5,13,20)))
								{
								$lin['status']  =  $wfstat['status'];
								$linedata[] = $lin;
								}
							}
	            	}
	         	}
         	}
         }
      }
   $headtitle = NAME_SITE . " - " . $formset['cname'];
   require (SITE_ELEMENT_DIR."box_htmlhead.php");
?>
<body>
<?php if (count($linedata)) { ?>
<script type="text/javascript">
function intoFlow() {
	if ($('[name="selected_t[]"]:checked').length)
		{
		var val = [];
		$('[name="selected_t[]"]:checked').each(function(i){ val[i] = $(this).val(); });
		openStudyPopup(val);
		}
	else
		alert("You haven't selected anything.");
}
</script>
<?php } ?>

   <div id="imgbuf" style="position:fixed;top:200px;left:200px;display:none;"><a href="#" id="imgLink"><img src="" id="bigImg" onmouseout="javascript:noImage(event);" ></a></div>
   <div id="smartbuf" style="position:fixed;top:200px;left:200px;display:none;"><a href="#" id="smartLink"><img src="images/smart.png" id="smartImg" onmouseout="javascript:noImage(event);" ></a></div>

   
<? require (SITE_ELEMENT_DIR."box_pagehead.php"); ?>
<script>
   var posX = 0;
   var posY = 0;
   function showImage(id,iurl,smart,align_left) {
      document.getElementById('imgbuf').style.top = posY+"px";
      document.getElementById('imgbuf').style.left = (align_left) ? posX+"px" : (posX - 271)+"px";
      document.getElementById('bigImg').src = iurl;
      document.getElementById('imgbuf').style.display = 'block';
      document.getElementById('bigImg').style.height = '360px';
      document.getElementById('bigImg').style.width = '480px';
      document.getElementById('imgLink').href = 'detail.php?id='+id;
	if (smart)
		{
	      document.getElementById('smartbuf').style.top = (posY+333)+"px";
      	document.getElementById('smartbuf').style.left = (align_left) ? (posX+390)+"px" : ((posX+390) - 271)+"px";
	      document.getElementById('smartbuf').style.display = 'block';
      	document.getElementById('smartImg').style.height = '27px';
	      document.getElementById('smartImg').style.width = '90px';
      	document.getElementById('smartLink').href = 'detail.php?id='+id;
		}
   }
   function noImage(event) {
      var x=event.clientX;
      var y=event.clientY;
      var curCursorX = x - (document.documentElement && document.documentElement.scrollLeft) || document.body.scrollLeft;
      var curCursorY = y - (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;
      if (x < posX || x > posX + 204 || y < posY || y  > posY + 153 )
         {
         document.getElementById('imgbuf').style.display = 'none';
         document.getElementById('imgbuf').style.top = '-9999px';
         document.getElementById('imgbuf').style.left = '-9999px';

         document.getElementById('smartbuf').style.display = 'none';
         document.getElementById('smartbuf').style.top = '-9999px';
         document.getElementById('smartbuf').style.left = '-9999px';
         }
   }
   function setPosXY(obj) {
      var curleft = 0;
      var curtop = 0;
      
      if (obj.offsetParent)
         {
         do {
            curleft += obj.offsetLeft;
            curtop += obj.offsetTop;
            } while (obj = obj.offsetParent);
         }
      posX = curleft - (document.documentElement && document.documentElement.scrollLeft) || document.body.scrollLeft;;
      posY = curtop - (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;;
   }
</script>
      <div id="bd">

         <!-- content -->
         <div class="content"><div class="content-inner clearfix">
            
      <h1><?php print $formset['cname']; ?></h1>
         <div class="pod-template-list"><div class="pod-template-list-inner"><div class="pod-template-list-inner-most" id="pod-template-list-inner">
            <table>
<?php
		$ii = 0;
		if (!count($linedata))
			print "<tr><td>There are currently no templates of this type in the database</td></tr>";
		else
			{
			foreach ($linedata as $lin)
				{
				if (!($ii++ % 4))
					print "<tr valign='top'>";
				$draftflag = (!in_array($lin['status'], array("Passed-QC", "Review-Stats", "Passed-Stats"))) ? "<span style='color:red'> [DRAFT]</span>" : "";
				$tlfval = sprintf("%s##TL%03d", $lin['tlfid'], $lin['id']);
				print '<td width="204"><a href="' . SITE_URL . '/detail.php?id=' . $lin['id'] . '">
					<img id="ssi' . $ii . '" onmouseout="javascript:noImage(event);" onmouseover="setPosXY(this);showImage(' . $lin['id'] . ',\'' . SITE_URL . '/uploads/' . $lin['screenshot'] . '\', ' . $lin['smart_yn'] . ', ' . ($ii % 4) . ');" src="' . SITE_URL . '/uploads/' . $lin['screenshot'] . '" width="204px" height="153px" style="padding:3px 5px"></a><br>
					<div style="width:204px;padding:0 9px;"><input type="checkbox" name="selected_t[]" id="selected_t" value="' . $tlfval . '">&nbsp;' .
					(($lin['smart_yn']) ? '<span style="font-weight:900;color:#8C7853;font-style:italic;">SMART</span>&nbsp;' : '') .
					'<a href="' . SITE_URL . '/detail.php?id=' . $lin['id'] . '">
					<span style="color:black;text-decoration:none;font-weight:bold;">' . $lin['title'] . $draftflag . '</span></div></a></td>';

				if (!($ii % 4))
					print "</tr>";
				}
			if ($ii % 4)
				print "</tr>";
			}
?>                
            </table>
         </div></div>
            </div><!--/main-column-->
<?php if (count($linedata)) { ?>
		<div style="margin-top:7px">
			<button onclick="javascript:intoFlow();">Copy Selected Template(s) into Project</button>
			<button style="margin-left:380px" onclick="javascript:openCommentary(0);">Provide Feedback</button>
		</div>
<?php } ?>

         </div></div><!--/content-->

      </div><!-- /body -->
      
<?php include (SITE_ELEMENT_DIR . "box_footer.php"); ?>  

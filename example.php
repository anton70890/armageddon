<? 
class nikeAjax { 
	public function index () {

		$map_id = $_POST['name'];
		$con = oci_connect($dbUser, $dbPass,$dbName); 		
		$query = "SELECT application_objects.id, x, y, type, caption, object_name, id_form, id_to, sign FROM application_objects LEFT JOIN application_links ON (application_links.id_from=application_objects.id) WHERE application_map_id=$map_id";
		$result = oci_parse($con, $query); 		
		oci_execute($result,OCI_DEFAULT);
		
		$img_srcs = array();
		$points = array();
		$connections = array();
		$captions = array ();
		
		while($rowNike = oci_fetch_assoc($result)){
			$x = $rowNike['X'];
			$y = $rowNike['Y'];
			$id = $rowNike['ID'];
			$type = $rowNike['TYPE'];
			$caption =  iconv('windows-1251', 'utf-8',$rowNike['CAPTION']);
			$object_name = $rowNike['OBJECT_NAME'];
			$id_form = $rowNike['ID_FORM'];
			$sign = $rowNike['SIGN'];
			$id_to = $rowNike['ID_TO'];
			
			if (!array_key_exists($id, $img_srcs)) {
				switch ($type){
					case 1:
						$img = "/controllers/maps/images/download_template.jpg";
						$img1 = "/controllers/maps/images/download_template_selected.jpg";
						$img2 = "/controllers/maps/images/download_template_selected_current.jpg";
						break;
					case 2:
						$img = "/controllers/maps/images/data_template.jpg";	
						$img1 = "/controllers/maps/images/data_template_selected.jpg";
						$img2 = "/controllers/maps/images/data_template_selected_current.jpg";
						break;
					case 3:
						$img = "/controllers/maps/images/download.jpg";		
						$img1 = "/controllers/maps/images/download_selected.jpg";		
						$img2 = "/controllers/maps/images/download_selected.jpg_current";	
						break;
					case 4:
						$img = "/controllers/maps/images/constraint.jpg";
						$img1 = "/controllers/maps/images/constraint_selected.jpg";
						$img2 = "/controllers/maps/images/constraint_selected_current.jpg";
						break;
					case 5:
						$img = "/controllers/maps/images/arrow.jpg";
						$img1 = "/controllers/maps/images/arrow.jpg_selected";
						$img2 = "/controllers/maps/images/arrow_selected_current.jpg";
						break;
					case 6: 
						$img = "/controllers/maps/images/protocols.jpg";
						$img1 = "/controllers/maps/images/protocols_selected.jpg";
						$img2 = "/controllers/maps/images/protocols_selected_current.jpg";
						break;
					case 7:
						$img = "/controllers/maps/images/diagram.jpg";	
                                                $img1 = "/controllers/maps/images/diagram_selected.jpg";							
						$img2 = "/controllers/maps/images/diagram_selected_current.jpg";							
						break;
					case 8:
						$img = "/controllers/maps/images/report.jpg";
						$img1 = "/controllers/maps/images/report_selected.jpg";
						$img2 = "/controllers/maps/images/report_selected_current.jpg";
						break;
					case 9:
						$img = "/controllers/maps/images/circle.jpg";
						$img1 = "/controllers/maps/images/circle_selected.jpg";
						$img2 = "/controllers/maps/images/circle_selected_current.jpg";
						break;
				
				}
				
				
				$point = new Point($x, $y);
				$points[$id] = $point;
				
				$connection = array();
				if ($id_to != null) {
					$connection[0] = new Connection($id_to, $sign);
				}
				$connections[$id] = $connection;
					
				$cap = new Text($caption);
				$captions[$id] = $cap;
				
				




			
				$img_srcs[$id] = '<img src="' . $img . '" src_selected="' . $img1 . '" src_current="' . $img2. '" type="' . $type . '" class="p5" caption="' .$caption . '" class = "click" id="'. $id .'"  onclick="currentimage(' . $id . ');func(' . $id . ')" alt="" ObjectName="'. $object_name . '" style="position:absolute;top:'. $y .'px ; left:'. $x .'px" ';
			} else {
				$connection = $connections[$id];
				if ($id_to != null) {
					$connection[1] = new Connection($id_to, $sign);
				}
				$connections[$id] = $connection;
			}			
			
			if ($sign == 1) {
				$img_srcs[$id] .= ' positive_id="' . $id_to . '"';
			} else if ($sign == -1) {
				$img_srcs[$id] .= ' negative_id="' . $id_to . '"';
			} else if ($sign == 0) {
				$img_srcs[$id] .= ' zero_id="' . $id_to . '"'; 
			} else {
				die("Неизвестынй sign = $sign");
			}			
		}
		foreach ($img_srcs as $key => $value) {
	

		draw_arrow($key, $con, $points, $connections, $captions); //Рисуем
		}
		foreach ($img_srcs as $key => $value) {
			echo $value . ">";

	
		}
		
		$z=count($img_srcs);
		reset($img_srcs);
		$k=key($img_srcs);
		$kz=$k+$z;
		
		?> 
		<script type="text/javascript">
		function BuildWindows (objName , zero) {
		
		var identCube = objName;
		var nextwindow = zero;
			if (identCube != 0) {
				var nameCube = $(this).html();
				$.dialogWindowModal({
                'title': nameCube,
				'content': '<iframe id = "malcev" zero_id="' + nextwindow  +'" name = "' + identCube + '" frameborder = "0" src = "/olap/Sys/PageObject.aspx?ID=' + identCube + '&mb=' + $.cookie('olapMetabase')+ '" style = "position: absolute; top: 32px; left: 0; height: 90%; width: 100%;"></iframe>',
			
				'height': '0.9'
				});
			} else {
				if (nextwindow !=0) {
				showallatrib (nextwindow);
				} else {
				throw "Конец карты";
				}
			
			}
		
		}
		
		
		
		
		</script>
		<?php
		echo '
		<script type="text/javascript">
		function showallatrib(k){
		
		//Запуск карты с начала
		
		var kz=' . $kz . ';
			
				for (k;k<kz;) 
				{
					
					$("#" + k).each( function(){ this.src = this.getAttribute("src_current"); } );
					type = $("#" + k).attr("type");
					alert (type);
					if (type != 9){
					var id = $("#" + k).attr("id"); 
					var caption = $("#" + k).attr("caption"); 
					var zero_id = $("#" + k).attr("zero_id");
					var ObjectName = $("#" + k).attr("ObjectName");
					
					if (zero_id == null){
					
					BuildWindows (ObjectName , zero_id);
					throw "Запуск загрузчика";} else {
					BuildWindows (ObjectName , zero_id);
					throw "Запуск загрузчика";
					
					
					}
		
					} else {
					var id = $("#" + k).attr("id"); 
					var caption = $("#" + k).attr("caption");
					var positive_id = $("#" + k).attr("positive_id");
					var negative_id = $("#" + k).attr("negative_id");
		
					function selectnextelem () {
					var a=Math.random();
					var b=Math.random();
					if (a<b){
					k=positive_id;
					} else {
					k=negative_id;
					}
					
					alert ("NextID="+k);
					showallatrib(k);
					}
					selectnextelem ();
					}
					
				}
				
			return;
		}
		
		function str_ends_with(str, srcselected) {
		return str.indexOf(srcselected, str.length - srcselected.length) !== -1;						
		}

		//Запуск карты с выделенного элемента
		function showpartmap () {
		$("#formap7 img").each(function(){
		var id=$(this).attr("id");
		var srcselected="_selected_current.jpg";
		var str=$(this).attr("src");
		if (str_ends_with(str, srcselected)){
		var that_id=$(this).attr("id");
		showallatrib (that_id);
		
		}
		
		});
		}
		
		
		</script>';
		
		echo '<button id="mapbutton" onclick="showallatrib(' . $k . ');" >Запуск карты с начала до конца</button>';

		echo '<button id="showpartmap" onclick="showpartmap();" >Запуск карты от выделенной операции</button>';

		?>

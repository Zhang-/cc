<?php

	/* 

	page function

	引入方法：
	
	<?php
		require_once(Yii::app()->basePath.'/extensions/pageFunction.php'); //引入本页面方法
	?>
		
	*/
	
	
	

/* ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ sysManage ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ */

	/* 
		sysManage Admin
	*/

	function getOneKeyBackButton()
	{
		if(HelpTool::checkActionAccess('backup'))
		{
			echo 
				CHtml::submitButton('一键备份',
					array(
					'class'=>'beifen',
					'style'=>'color:red;width:66px;',
					'id'=>'onekeyback',
					'onclick'=>'{if(confirm("备份数据库时建议不要进行其他操作,确定继续吗?")){$("#dbBackupPage").dialog( "open" );}else{return false;}}'
					)
				);
		}else{
			echo '';
		}
	}
	
	//权限控制批量操作按钮可见与否
	function getChecksNameSA()
	{
		if(HelpTool::checkActionAccess('delete_back'))
		{
			echo
				'<th>
					<div class="checks">
						<input class="all" type="checkbox" id="allcheckbtn"/>
						<span title="批量操作" class="option" id="alertwindows"></span>
						<div class="allselect">
							<div class="headp">勾选项批量操作</div>
							<p class="p2" onclick="javascript:delcheckbackup();"><span >全部删除</span></p>
							<p class="p2" onclick="javascript:cancelcheck();">取消</p>
						</div>
					</div>
				</th>';
		}else{
			echo '';
		}
	}
	
	function getChecksAvailableSA($backupData)
	{
		//权限控制批量操作按钮可见与否
		if(HelpTool::checkActionAccess('delete_back'))
		{
			echo 
				" <td class='first_td'>
					<div class='checks'><input type='checkbox' name='checkboxs[]' value=".$backupData['name']."/>
						<div class='config'>
							<span class='sp normal'></span>	 
							<div class='down'>
								<span>";
								echo 
									CHtml::link('删除备份', 
										array(
											'/databack/delete_back',
											'file'=>$backupData['name'],
										),
										array(
											"title"=>"删除",
											"id"=>"deleteBackupButton",
											'onclick'=>"{if(confirm('您真的要删除吗?')){return true;}return false;}"
										)
									);
							echo 
								"</span>
								<span>";
								echo 
									CHtml::link('下载备份',
										array(
											'/databack/downloadbak',
											'file'=>$backupData['name']
										),
										array(
											'title'=>'点击下载' ,
											'id'=>'downloadBackupButton'.$backupData['id'],
										)
									);
							echo
								"</span>
							</div>
						</div>
					</div>
				</td>";
		}else{
			echo '';
		}
	}
	
	function getEmptyTableSA()
	{
		echo 
			'<tr class="odd selected">
				<td colspan="4">
					<span class="empty">没有找到数据.</span>
				</td>
			</tr>';
	}
	
	function getBackupTable($data,$thisURL)
	{
		echo '<tbody>';
			if(array_filter($data)==null)
			{
				getEmptyTableSA();
			}else{
				$cutData=array_filter($data);
				krsort($cutData);
		 
				$perNumber=10; //每页显示的记录数
				if(isset($_GET['page']))
				{
					$page=$_GET['page'];
				}else{
					$page=1;
				}
				$count= count($cutData);//获得记录总数
				$totalPage=ceil($count/$perNumber); //计算出总页数
				$startCount=($page-1)*$perNumber; //分页开始,根据此方法计算出开始的记录
				$thisPageData=array_slice($cutData,$startCount,$perNumber);
					
				$i=1;
				foreach($thisPageData as $backupData)
				{ 
					$backupData['id']=$i;
					$i++;
				
					echo '<tr>';
							getChecksAvailableSA($backupData);
						echo '<td>'.$backupData['name'].'</td>';
						echo '<td>'.$backupData['size'].'</td>';
						echo '<td>'.$backupData['time'].'</td>';
						echo '</tr>';
				}
			} 
		echo '</tbody>
		</table>';

		if(array_filter($data)!=null && $count>10)
		{ 
			echo '<div class="pager">
					<ul id="yw1" class="yiiPager">
					<li class="first">
						<a href="'.$thisURL.'">首页</a>
					</li>';
					
			if ($page != 1) //页数不等于1
			{ 
				echo '<li class="previous">
						<a href="'.$thisURL."&page=".($page - 1).'">上一页</a>
					</li>';
			}
					
			for ($i=1;$i<=$totalPage;$i++) //循环显示出页面
			{  
				if($page==$i){
					echo '<li class="page selected">';
				}else{ 
					echo '<li class="page">';
				}
				echo '<a href="'.$thisURL."&page=".$i.'">'.$i.'</a></li>';
			}
					
			if ($page<$totalPage)  //如果page小于总页数,显示下一页链接
			{
				echo '<li class="next">
						<a href="'.$thisURL."&page=".($page + 1).'">下一页</a>
					</li>';
			}
				
			if($page!=$totalPage)
			{
				echo '<li class="last">';
			}else{
				echo '<li class="last hidden">';
			}	

			echo '<a href="'.$thisURL."&page=".($totalPage).'">尾页</a>
					</li>
				</ul>
			</div>';
		}
	}
	
	/* 
		sysManage UserAdmin
	*/
	
	//用户组名称汉化
	function getItemname($itemname)
	{
		$allRoleTrans = HelpTool::getRolesTrans();
		$thisRoleTrans = isset($allRoleTrans[ $itemname ]) ? $allRoleTrans[ $itemname ] : $itemname ;
		return $thisRoleTrans;
	}

	//权限控制批量操作按钮可见与否
	function getChecksAvailableSU(){
		if(HelpTool::checkActionAccess('sysmanageuserdelete'))
		{
			return true;
		}else{
			return false;
		}
	}
	
	//权限控制查看按钮可见与否
	function getChecksViewAvailableSU(){
		if(HelpTool::checkActionAccess('sysmanageuserdelete'))
		{
			return false;
		}else{
			return true;
		}
	}
	
	function getButtonSU($id,$thisUserID){
		
		echo "  
			<div class='checks'><input name='checkboxs[]' type='checkbox' value=".$id." /> 
				<div class='config'>
					<span class='sp normal'></span>
					<div class='down'>
						<span onclick='viewUserInfo(".$id.")' >用户详情</span>
						<span onclick='updateUserInfo(".$id.")'>用户信息修改</span>";
						if( $thisUserID != $id)
						{
							echo "<span>";
							echo CHtml::link('删除用户', 
								array(
									'/sysmanage/userdelete', 
									'id'=>$id,
								),
								array(
									"title"=>"删除",
									"id"=>"deleteUserButton",
									'onclick'=>"{if(confirm('您真的要删除吗?')){return true;}return false;}"
								)
							);
							echo "</span>";
						}
					echo "</div>
				</div>
			</div>";
	}
		
	//查看按钮
	function getViewButtonSU($id){
		echo " 
			<div class='checks'>			
				<div style='left:21px' class='config'>
					<span class='sp normal'></span>
					<div class='down'>
						<span onclick='viewUserInfo(".$id.")' >用户详情</span>
					</div>
				</div>
			</div>";
	}
	
	
	/* 
		sysManage UserLogAdmin
	*/
	
	//操作类型汉化
	function getLogTypeTrans($type)
	{
		return HelpTool::getLogTypeTrans($type);
	}

	//权限控制批量操作按钮可见与否
	function getChecksAvailableSULA()
	{
		if(HelpTool::checkActionAccess('sysmanageuserlogdelete'))
		{
			return true;
		}else{
			return false;
		}
	}
	
	function getChecksViewAvailableSULA()
	{
		if(HelpTool::checkActionAccess('sysmanageuserlogdelete'))
		{
			return false;
		}else{
			return true;
		}
	}

	function getButtonSULA($id)
	{
		echo "  
			<div class='checks'>
				<input name='checkboxs[]' type='checkbox' value=".$id." />
				<div class='config'>
					<span class='sp normal'></span>
					<div class='down'>
						<span onclick='viewUserLogInfo(".$id.")' >日志详情</span>
						<span>";
					echo 
						CHtml::link('删除', 
							array(
								'/sysmanage/userlogdelete',
								'id'=>$id,
							),
							array(
								"title"=>"删除",
								"id"=>"deleteUserLogs",
								'onclick'=>"{if(confirm('您真的要删除吗?')){return true;}return false;}"
							)
						);	
					echo 	
						"</span>
					</div>
				</div>
			</div>";
	}
		 
	function getViewButtonSULA($id){
		echo 
			"<div class='checks'>
				<div style='left:21px' class='config'>
					<span class='sp normal'></span>
					<div class='down'>
						<span onclick='viewUserLogInfo(".$id.")' >日志详情</span>
					</div>
				</div>
			</div>";
	} 
	
	function getLogURL($url,$type)
	{
		if($type=='View')
		{
			$thisURL='<a style="text-decoration:underline;" href="'.$url.'" target="_blank" >'.$url.'</a>';
			return $thisURL;
		}else{
			return $url;
		}
	}
	
		/* 
		sysManage inputStatement
	*/
	
	//时间戳转换
	function getDateTime($strtotime)
	{
		return date('Y-m-d H:i:s',$strtotime);
	}
	
	//权限控制批量操作按钮可见与否
	function getChecksAvailableSISA()
	{
		if(HelpTool::checkActionAccess('sysmanagestatementdelete'))
		{
			return true;
		}else{
			return false;
		}
	}
	
	function getChecksViewAvailableSISA()
	{
		if(HelpTool::checkActionAccess('sysmanagestatementdelete'))
		{
			return false;
		}else{
			return true;
		}
	}
	
	function getButtonSISA($id)
	{
		echo "  
			<div class='checks'>
				<input name='checkboxs[]' type='checkbox' value=".$id." />
				<div class='config'>
					<span class='sp normal'></span>
					<div class='down'>
						<span onclick='viewStatementInfo(".$id.")' >口径详情</span>
						<span onclick='updateStatementInfo(".$id.")'>口径信息修改</span>
						<span>";
					echo 
						CHtml::link('删除', 
							array(
								'/sysmanage/statementdelete',
								'id'=>$id,
							),
							array(
								"title"=>"删除",
								"id"=>"deleteStatement",
								'onclick'=>"{if(confirm('您真的要删除吗?')){return true;}return false;}"
							)
						);	
					echo 	
						"</span>
					</div>
				</div>
			</div>";
	}
		 
	function getViewButtonSISA($id){
		echo 
			"<div class='checks'>
				<div style='left:21px' class='config'>
					<span class='sp normal'></span>
					<div class='down'>
						<span onclick='viewStatementInfo(".$id.")' >口径详情</span>
					</div>
				</div>
			</div>";
	}

	/**
	* Sysmanage System-Alerts-Admin 生成单行操作按钮
	*/
	function getButtonSSAA($id)
	{
		echo "  
			<div class='checks'>
				<input name='checkboxs[]' type='checkbox' value=".$id." />
				<div class='config'>
					<span class='sp normal'></span>
					<div class='down'>
						<span onclick='viewAlertsInfo(".$id.")' >提醒内容详情</span>
						<span onclick='updateAlertsInfo(".$id.")'>提醒内容修改</span>
						<span>";
					echo 
						CHtml::link('删除提醒', 
							array(
								'/sysmanage/alertsDelete',
								'id'=>$id,
							),
							array(
								"title"=>"删除提醒",
								"id"=>"alertsDelete",
								'onclick'=>"{if(confirm('您真的要删除吗?')){return true;}return false;}"
							)
						);	
					echo 	
						"</span>
					</div>
				</div>
			</div>";
	}
	
	//
    /**
	 * @name 获取内容过长单元格缩短显示样式
	 * @param string $length 单元格固定宽度
	 * @param string $cellData 单元格内容
	 * @param string $ziiType Yii小物件的类型
	 * @author 张洪源
	 * @create date 2013-06-16 12:32:36
	 */
	function getShortCellContent($length,$cellData,$ziiType)
	{
		$cellData = 
			"<span 
				title='".$cellData."' 
				style='width:".$length."px;
				white-space:nowrap;
				word-break:keep-all;
				overflow:hidden;
				text-overflow:ellipsis;
				height:14px;
				line-height:14px;'
			>".$cellData."</span>";
			
		if($ziiType == 'CDetailView')
		{
			return $cellData;
		}else{
			echo $cellData;
		}		
	}

/* ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ sysManage ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ */

/* ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓  ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ */
/* ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑  ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ */	

/* ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓  ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ */
/* ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑  ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ */		
	
?>
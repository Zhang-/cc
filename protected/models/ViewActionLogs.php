<?php

/**
 * This is the model class for table "view_action_logs".
 *
 * The followings are the available columns in table 'view_action_logs':
 * @property integer $id
 * @property string $actiontime
 * @property string $userip
 * @property integer $userid
 * @property string $username
 * @property string $userrole
 * @property string $actiontype
 * @property string $affectid
 * @property string $url
 */
class ViewActionLogs extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ViewActionLogs the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'view_action_logs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userip, userid, username, userrole, actiontype', 'required'),
			array('userid', 'numerical', 'integerOnly'=>true),
			array('userip, actiontype', 'length', 'max'=>20),
			array('username, userrole', 'length', 'max'=>30),
			array('actiontime, affectid, url', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, actiontime, userip, userid, username, userrole, actiontype, affectid, url', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'actiontime' => '记录时间',
			'userip' => '用户IP',
			'userid' => '用户ID',
			'username' => '用户名',
			'userrole' => '用户角色',
			'actiontype' => '操作类型',
			'affectid' => '受影响项ID',
			'url' => '操作链接',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('actiontime',$this->actiontime,true);
		$criteria->compare('userip',$this->userip,true);
		$criteria->compare('userid',$this->userid);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('userrole',$this->userrole,true);
		$criteria->compare('actiontype',$this->actiontype,false);
		$criteria->compare('affectid',$this->affectid,true);
		$criteria->compare('url',$this->url,true);

		$isAdmin = HelpTool::isAdmin(); //是否为Admin
		
		if($isAdmin['flag'] == false )
		{
			$criteria->addCondition('userrole <> '."'".$isAdmin['Admin']."'"); //添加非Admin搜索条件
		}
		
		return new CActiveDataProvider($this, array(
		'pagination'=>array(
            	'pageSize'=>10,//设置每页显示条数
        	),
			'sort'=>array(
				'defaultOrder'=>'actiontime DESC', //设置默认排序
			),
			'criteria'=>$criteria,
		));
	}
}
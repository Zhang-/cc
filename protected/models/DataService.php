<?php

/**
 * This is the model class for table "data_service".
 *
 * The followings are the available columns in table 'data_service':
 * @property integer $id
 * @property integer $staticID
 * @property string $startDateTime
 * @property string $stopDateTime
 */
class DataService extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DataService the static model class
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
		return 'data_service';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('staticID, startDateTime', 'required'),
			array('staticID', 'numerical', 'integerOnly'=>true),
			array('stopDateTime', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, staticID, startDateTime, stopDateTime', 'safe', 'on'=>'search'),
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
			'staticID' => 'Static',
			'startDateTime' => '开始时间',
			'stopDateTime' => '结束时间',
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

		$md = isset($_GET['md'])?$_GET['md']:'2';
		$sendByte = Yii::app()->params->sendByte; //收发字节参数
		
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('staticID',$this->staticID);

		
		$criteria->addCondition(" stopDateTime > startDateTime and (startDateTime >= '".$this->startDateTime."' and stopDateTime <= '".$this->stopDateTime."') and id in (select t1.id from data_service t1 where (SELECT SUM(upload_traffic+download_traffic) FROM dynamic_information t4 WHERE dataID<>0 AND netType=".$md." AND (download_traffic+upload_traffic)>0 AND t1.id=t4.dataID GROUP BY dataID)< ".($sendByte*1024).")");

		return new CActiveDataProvider($this, array(
			'pagination'=>array(
            	'pageSize'=>5,//设置每页显示条数
        	),
			'sort'=>array(
				'defaultOrder'=>'startDateTime DESC', //设置默认排序
			),
			'criteria'=>$criteria,
		));
	}


	/**
	 * @name 按时间计数
	 * @author zhanghy
	 */
	public function getDayServiceNum($sid = "", $startTime = '', $endTime = '')
	{

		$return = array();
		$result = $this->findAll(array(
			'condition'=>"staticID=$sid and startDateTime between '$startTime' and '$endTime'",
			'select'=>"DATE_FORMAT( startDateTime, '%Y-%m-%d' ) AS date, COUNT(*) AS num",
			'group'=>"date",
			'order'=>"date",
		));
		if(!!$result)
			foreach($result as $i=>$data)
				print_r($data);exit;
    			$return[$data->attributes['date']]=$data->attributes;
    	print_r($return);exit;

	}
}
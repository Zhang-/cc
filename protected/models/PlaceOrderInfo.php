<?php

/**
 * This is the model class for table "place_order_info".
 *
 * The followings are the available columns in table 'place_order_info':
 * @property string $id
 * @property string $tid
 * @property string $did
 * @property string $start
 * @property string $startname
 * @property string $destination
 * @property string $desname
 * @property string $o_voice
 * @property integer $o_type
 * @property integer $o_status
 * @property integer $o_time
 * @property string $phone_num
 * @property integer $car_type
 * @property string $delivery_time
 * @property integer $need_porter
 * @property string $o_message
 */
class PlaceOrderInfo extends CActiveRecord
{

	CONST ORDERINFOKEY    = 'user:order:';     //订单信息 string key
	CONST USERORDERSKEY   = 'user:orders:';    //用户所有订单 hash key
	CONST DRIVERORDERSKEY = 'driver:orders:';  //司机所有订单 hash key

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PlaceOrderInfo the static model class
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
		return 'place_order_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('start, destination', 'required'),
			array('o_type, o_status, o_time, car_type, need_porter', 'numerical', 'integerOnly'=>true),
			array('tid, did, phone_num', 'length', 'max'=>19),
			array('start, destination', 'length', 'max'=>60),
			array('startname', 'length', 'max'=>90),
			array('o_voice', 'length', 'max'=>255),
			array('desname, delivery_time, o_message', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tid, did, start, startname, destination, desname, o_voice, o_type, o_status, o_time, phone_num, car_type, delivery_time, need_porter, o_message', 'safe', 'on'=>'search'),
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
			'tid' => '终端ID',
			'did' => '司机ID',
			'start' => '起点经纬度',
			'startname' => '起始点',
			'destination' => '终点经纬度',
			'desname' => '终点',
			'o_voice' => '语音',
			'o_type' => '订单类型',
			'o_status' => '订单状态',
			'o_time' => '订单时间',
			'phone_num' => '联系电话',
			'car_type' => '车型',
			'delivery_time' => '配送时间',
			'need_porter' => '搬运服务',
			'o_message' => '订单留言',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('tid',$this->tid,true);
		$criteria->compare('did',$this->did,true);
		$criteria->compare('start',$this->start,true);
		$criteria->compare('startname',$this->startname,true);
		$criteria->compare('destination',$this->destination,true);
		$criteria->compare('desname',$this->desname,true);
		$criteria->compare('o_voice',$this->o_voice,true);
		$criteria->compare('o_type',$this->o_type);
		$criteria->compare('o_status',$this->o_status);
		$criteria->compare('o_time',$this->o_time);
		$criteria->compare('phone_num',$this->phone_num,true);
		$criteria->compare('car_type',$this->car_type);
		$criteria->compare('delivery_time',$this->delivery_time,true);
		$criteria->compare('need_porter',$this->need_porter);
		$criteria->compare('o_message',$this->o_message,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
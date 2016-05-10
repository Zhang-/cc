<?php

/**
 * This is the model class for table "user_information".
 *
 * The followings are the available columns in table 'user_information':
 * @property string $id
 * @property string $start_data_time
 * @property string $time
 * @property string $phone_brand
 * @property string $app_version
 * @property string $os_version
 * @property string $imsi
 * @property string $imei
 * @property string $modified_at
 * @property string $active_count
 * @property string $city_id
 * @property string $terminal_config
 * @property string $config_time
 * @property string $net_type
 * @property string $change_time
 * @property string $phone_num
 */
class UserInformation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserInformation the static model class
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
		return 'user_information';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('time, imsi, imei, phone_num', 'required'),
			array('time, active_count, city_id, config_time, phone_num', 'length', 'max'=>19),
			array('phone_brand', 'length', 'max'=>90),
			array('app_version', 'length', 'max'=>30),
			array('os_version', 'length', 'max'=>60),
			array('imsi, imei', 'length', 'max'=>45),
			array('terminal_config', 'length', 'max'=>1073741823),
			array('net_type', 'length', 'max'=>15),
			array('start_data_time, modified_at, change_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, start_data_time, time, phone_brand, app_version, os_version, imsi, imei, modified_at, active_count, city_id, terminal_config, config_time, net_type, change_time, phone_num', 'safe', 'on'=>'search'),
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
			'start_data_time' => 'Start Data Time',
			'time' => 'Time',
			'phone_brand' => 'Phone Brand',
			'app_version' => 'App Version',
			'os_version' => 'Os Version',
			'imsi' => 'Imsi',
			'imei' => 'Imei',
			'modified_at' => 'Modified At',
			'active_count' => 'Active Count',
			'city_id' => 'City',
			'terminal_config' => 'Terminal Config',
			'config_time' => 'Config Time',
			'net_type' => 'Net Type',
			'change_time' => 'Change Time',
			'phone_num' => 'Phone Num',
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
		$criteria->compare('start_data_time',$this->start_data_time,true);
		$criteria->compare('time',$this->time,true);
		$criteria->compare('phone_brand',$this->phone_brand,true);
		$criteria->compare('app_version',$this->app_version,true);
		$criteria->compare('os_version',$this->os_version,true);
		$criteria->compare('imsi',$this->imsi,true);
		$criteria->compare('imei',$this->imei,true);
		$criteria->compare('modified_at',$this->modified_at,true);
		$criteria->compare('active_count',$this->active_count,true);
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('terminal_config',$this->terminal_config,true);
		$criteria->compare('config_time',$this->config_time,true);
		$criteria->compare('net_type',$this->net_type,true);
		$criteria->compare('change_time',$this->change_time,true);
		$criteria->compare('phone_num',$this->phone_num,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/*保存用户信息*/
	/*public function saveUserInfo($attrs)
	{
		$this->attributes = $attrs;

		if ($this->save()) 
		{
			CacheR::getInstance(CacheR::USERDSN)->h
			//CacheFile::operatorList ( true ); // 更新数据接收方缓存
			//HelpTool::getActionInfo ( $model->id, 2 ); // 新建操作
			//$this->redirect ( array ('create','id' => $model->id) );
		}
	}*/
}
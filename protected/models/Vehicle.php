<?php

/**
 * This is the model class for table "Vehicle".
 *
 * The followings are the available columns in table 'Vehicle':
 * @property integer $id
 * @property integer $ownerId
 * @property string $carNumber
 * @property string $VIN
 * @property integer $carType
 * @property string $carModel
 * @property string $carAddress
 * @property string $gpsInfo
 * @property string $province
 * @property string $city
 * @property string $county
 * @property string $address
 */
class Vehicle extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Vehicle the static model class
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
		return 'Vehicle';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'required'),
			array('id, ownerId, carType', 'numerical', 'integerOnly'=>true),
			array('carNumber, province, city, county', 'length', 'max'=>10),
			array('VIN', 'length', 'max'=>17),
			array('carModel', 'length', 'max'=>20),
			array('carAddress, address', 'length', 'max'=>100),
			array('gpsInfo', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, ownerId, carNumber, VIN, carType, carModel, carAddress, gpsInfo, province, city, county, address', 'safe', 'on'=>'search'),
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
			'ownerId' => 'Owner',
			'carNumber' => 'Car Number',
			'VIN' => 'Vin',
			'carType' => 'Car Type',
			'carModel' => 'Car Model',
			'carAddress' => 'Car Address',
			'gpsInfo' => 'Gps Info',
			'province' => 'Province',
			'city' => 'City',
			'county' => 'County',
			'address' => 'Address',
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
		$criteria->compare('ownerId',$this->ownerId);
		$criteria->compare('carNumber',$this->carNumber,true);
		$criteria->compare('VIN',$this->VIN,true);
		$criteria->compare('carType',$this->carType);
		$criteria->compare('carModel',$this->carModel,true);
		$criteria->compare('carAddress',$this->carAddress,true);
		$criteria->compare('gpsInfo',$this->gpsInfo,true);
		$criteria->compare('province',$this->province,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('county',$this->county,true);
		$criteria->compare('address',$this->address,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
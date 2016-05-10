<?php

/**
 * This is the model class for table "gis_config".
 *
 * The followings are the available columns in table 'gis_config':
 * @property integer $id
 * @property string $type
 * @property integer $level
 * @property string $clon
 * @property string $clat
 * @property string $dataurl
 * @property string $mapurl
 */
class GisConfig extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GisConfig the static model class
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
		return 'gis_config';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, level, clon, clat, dataurl, mapurl', 'required'),
			array('level', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>10),
			array('clon, clat', 'length', 'max'=>20),
			array('dataurl, mapurl', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, level, clon, clat, dataurl, mapurl', 'safe', 'on'=>'search'),
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
			'type' => 'Type',
			'level' => 'Level',
			'clon' => 'Clon',
			'clat' => 'Clat',
			'dataurl' => 'Dataurl',
			'mapurl' => 'Mapurl',
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
		$criteria->compare('type',$this->type,true);
		$criteria->compare('level',$this->level);
		$criteria->compare('clon',$this->clon,true);
		$criteria->compare('clat',$this->clat,true);
		$criteria->compare('dataurl',$this->dataurl,true);
		$criteria->compare('mapurl',$this->mapurl,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
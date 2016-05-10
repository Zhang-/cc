<?php

/**
 * This is the model class for table "excel_error_info".
 *
 * The followings are the available columns in table 'excel_error_info':
 * @property integer $id
 * @property string $error_jason
 * @property string $time
 * @property string $type
 * @property string $operate
 * @property string $state
 */
class ExcelErrorInfo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ExcelErrorInfo the static model class
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
		return 'excel_error_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, operate, state', 'required'),
			array('type, operate', 'length', 'max'=>10),
			array('state', 'length', 'max'=>100),
			array('error_jason, time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, error_jason, time, type, operate, state', 'safe', 'on'=>'search'),
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
			'error_jason' => 'Error Jason',
			'time' => 'Time',
			'type' => 'Type',
			'operate' => 'Operate',
			'state' => 'State',
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
		$criteria->compare('error_jason',$this->error_jason,true);
		$criteria->compare('time',$this->time,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('operate',$this->operate,true);
		$criteria->compare('state',$this->state,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>7,
				),
		));
	}
}
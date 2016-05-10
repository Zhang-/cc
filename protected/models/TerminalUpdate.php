<?php

/**
 * This is the model class for table "terminal_update".
 *
 * The followings are the available columns in table 'terminal_update':
 * @property integer $id
 * @property integer $static_information_id
 * @property string $updateVersion
 * @property string $appVersion
 * @property string $appUrl
 * @property string $appDes
 * @property string $confVersion
 * @property string $confUrl
 * @property string $appName
 * @property string $md5Check
 */
class TerminalUpdate extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TerminalUpdate the static model class
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
		return 'terminal_update';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('static_information_id', 'numerical', 'integerOnly'=>true),
			array('updateVersion, appVersion, appUrl, appDes, confVersion, confUrl, appName, md5Check', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, static_information_id, updateVersion, appVersion, appUrl, appDes, confVersion, confUrl, appName, md5Check', 'safe', 'on'=>'search'),
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
			'static_information_id' => 'Static Information',
			'updateVersion' => 'Update Version',
			'appVersion' => 'App Version',
			'appUrl' => 'App Url',
			'appDes' => 'App Des',
			'confVersion' => 'Conf Version',
			'confUrl' => 'Conf Url',
			'appName' => 'App Name',
			'md5Check' => 'Md5 Check',
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
		$criteria->compare('static_information_id',$this->static_information_id);
		$criteria->compare('updateVersion',$this->updateVersion,true);
		$criteria->compare('appVersion',$this->appVersion,true);
		$criteria->compare('appUrl',$this->appUrl,true);
		$criteria->compare('appDes',$this->appDes,true);
		$criteria->compare('confVersion',$this->confVersion,true);
		$criteria->compare('confUrl',$this->confUrl,true);
		$criteria->compare('appName',$this->appName,true);
		$criteria->compare('md5Check',$this->md5Check,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
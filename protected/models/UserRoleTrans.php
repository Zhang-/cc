<?php

/**
 * This is the model class for table "user_role_trans".
 *
 * The followings are the available columns in table 'user_role_trans':
 * @property integer $id
 * @property string $rolename
 * @property string $roletrans
 * @property string $createtime
 * @property string $annotation
 * @property string $roletasks
 */
class UserRoleTrans extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserRoleTrans the static model class
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
		return 'user_role_trans';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('rolename', 'length', 'max'=>15),
			array('roletrans', 'length', 'max'=>25),
			array('createtime, annotation, roletasks', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, rolename, roletrans, createtime, annotation, roletasks', 'safe', 'on'=>'search'),
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
			'rolename' => 'Rolename',
			'roletrans' => '角色名称',
			'createtime' => '创建时间',
			'annotation' => '角色描述',
			'roletasks' => 'Roletasks',
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
		$criteria->compare('rolename',$this->rolename,true);
		$criteria->compare('roletrans',$this->roletrans,true);
		$criteria->compare('createtime',$this->createtime,true);
		$criteria->compare('annotation',$this->annotation,true);
		$criteria->compare('roletasks',$this->roletasks,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
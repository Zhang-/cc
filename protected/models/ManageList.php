<?php

/**
 * This is the model class for table "manage_list".
 *
 * The followings are the available columns in table 'manage_list':
 * @property integer $userid
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property string $avatar
 * @property string $province
 * @property string $city
 * @property string $address
 * @property string $email
 * @property string $phone
 * @property string $regDateTime
 * @property string $itemname
 * @property string $expansion
 * @property string $bizrule
 * @property string $data
 */
class ManageList extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ManageList the static model class
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
		return 'manage_list';
	}

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username','unique'),
			array('username, password', 'required'),
			array('username, phone', 'length', 'max'=>20),
			array('password, salt, avatar', 'length', 'max'=>255),
			array('province, city, email, itemname', 'length', 'max'=>50),
			array('address', 'length', 'max'=>100),
			array('lastLoginTime, thisLoginTime, expansion, bizrule, data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('userid, username, password, salt, avatar, province, city, address, email, phone, regDateTime, lastLoginTime, thisLoginTime, itemname, expansion, bizrule, data', 'safe', 'on'=>'search'),
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
			'userid' => 'Userid',
			'username' => '用户名',
			'password' => '密码',
			'salt' => 'Salt',
			'avatar' => 'Avatar',
			'province' => '省份',
			'city' => '城市',
			'address' => '地址',
			'email' => 'Email',
			'phone' => '电话',
			'regDateTime' => '注册时间',
			'itemname' => '用户组',
			'expansion' => 'Expansion',
			'bizrule' => 'Bizrule',
			'data' => 'Data',
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
		
		$criteria->compare('userid',$this->userid);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('salt',$this->salt,true);
		$criteria->compare('avatar',$this->avatar,true);
		$criteria->compare('province',$this->province,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('regDateTime',$this->regDateTime,true);
		$criteria->compare('itemname',$this->itemname,true);
		$criteria->compare('expansion',$this->expansion,true);
		$criteria->compare('bizrule',$this->bizrule,true);
		$criteria->compare('data',$this->data,true);
		
		$isAdmin = HelpTool::isAdmin(); //是否为Admin
		
		if($isAdmin['flag'] == false )
		{
			$criteria->addCondition('itemname<>"'.$isAdmin['Admin'].'"'); //添加非Admin搜索条件
		}
		
		return new CActiveDataProvider($this, array(
			'pagination'=>array(
            	'pageSize'=>10,//设置每页显示条数
        	),
			'sort'=>array(
				'defaultOrder'=>'regDateTime DESC', //设置默认排序
			),
			'criteria'=>$criteria,
		));
	}
}
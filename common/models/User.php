<?php

namespace common\models;

use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public $password_repeat;
    public $rememberMe;
    public $authKey;

    public static function tableName() {
        return 'users';
    }

    public function attributeLabels() {
        return [
            'username' => 'Логин:',
            'password' => 'Повторите пароль:',
            'password_repeat' => 'Пароль:',
            'email' => 'Email:'
        ];
    }

    public function rules() {
        return [
            [['username', 'password', 'email', 'password_repeat'], 'required', 'message' => 'Поле не должно быть пустым!'],
            ['username', 'string', 'max' => '45', 'message' => 'Недопустимый формат логина!'],
            ['username', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'username',
                'message' => 'Пользователь с данным логином уже существует!'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'email',
                'message' => 'Пользователь с данным email уже зарегистрирован!'],
            [['password', 'password_repeat'], 'string', 'max' => '255', 'message' => 'Недопустимый формат пароля!'],
            ['password', 'compare', 'message' => 'Введенные пароли не совпадают!'],
            ['rememberMe', 'boolean'],
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id) {
        return static ::findOne($id);
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        return static ::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId() {
        return $this -> getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey() {
        return $this -> authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey) {
        return $authKey === $this -> getAuthKey();
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        if (!\Yii::$app -> security -> validatePassword($password, $this -> password)) {
            $this -> addError('password', 'Логин или пароль неверны');
            return false;
        }
        return true;
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this -> password = \Yii::$app -> security -> generatePasswordHash($this -> password);
            if ($this -> isNewRecord) {
                $this -> access_token = \Yii::$app ->security-> generateRandomString();
            }
            return true;
        } else {
            return false;
        }
    }

    public function setName($name) {
        $this -> username  = $name;
    }

    public function getTeams() {
        return $this -> hasMany(UsersTeams::class, ['id_user' => 'id']);
    }
}
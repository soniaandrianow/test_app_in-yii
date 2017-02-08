<?php

namespace app\models;

use Exception;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $firstname
 * @property string $lastname
 * @property integer $isadmin
 */
class Users extends ActiveRecord implements IdentityInterface
{

    /**
     * @inheritdoc
     */
    public $password_repetition;
    public $newpassword;
    public $newpassword_repetition;

    const SCENARIO_UPDATE = 'update';
    const SCENARIO_UPDATE_USERNAME = 'update_username';
    const SCENARIO_UPDATE_PASSWORD = 'update_password';

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        //$scenarios[]
        $scenarios[self::SCENARIO_UPDATE] = ['username', 'firstname', 'lastname'];
        $scenarios[self::SCENARIO_UPDATE_USERNAME] = ['username', 'firstname', 'lastname'];
        $scenarios[self::SCENARIO_UPDATE_PASSWORD] = ['newpassword', 'newpassword_repetition'];
        return $scenarios;
    }

    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'firstname', 'lastname'], 'required'],
            [['isadmin'], 'integer'],
            [['token'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 30],
            [['password'], 'string', 'max' => 255],
            [['firstname'], 'string', 'max' => 50],
            [['lastname'], 'string', 'max' => 100],
            [['username'], 'unique'],
            ['password_repetition', 'required'],
            [['password_repetition'], 'string', 'max' => 255],
            ['password_repetition', 'compare', 'compareAttribute' => 'password'],
            //[['password'], 'match', 'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/'],
            [['password'], 'validatePwd'],
            [['username'], 'match', 'pattern' => '/^[a-zA-Z][a-zA-Z0-9]*$/'],
            ['newpassword_repetition', 'compare', 'compareAttribute' => 'newpassword', 'on' => 'update_password'],
            [['newpassword'], 'string', 'max' => 255],
            ['newpassword', 'validatePwd'],
            ['newpassword', 'required', 'on' => 'update_password'],
        ];
    }

    public function validatePwd($attribute)
    {
        if (!preg_match('/^(?=.*\p{Ll})(?=.*\p{Lu})(?=.*\p{Nd})[\p{L}\p{Nd}]{8,}$/u', $this->$attribute)) {
            $this->addError($attribute, 'Password invalid');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'isadmin' => 'Isadmin',
            'token' => 'Token'
        ];
    }

    /**
     * hash for password
     * @param string $value
     * @return type
     */
    public function hash($value)
    {
        return Yii::$app->getSecurity()->generatePasswordHash($value);
    }

    /**
     * Updates password in model to encrypted one.
     * Creates token for user.
     * @param type $insert
     * @return boolean
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->token = \Yii::$app->security->generateRandomString();
                $this->password = $this->hash($this->password);
            } elseif ($this->newpassword !== '') {
                $this->password = $this->hash($this->newpassword);
            }
            return true;
        }
        return false;
    }

    public static function findByUsername($username)
    {
        $user = Users::find()
                ->where(['username' => $username])
                ->one();
        return new static($user);
    }

    /**
     * Check if password provided by user during log in is same as in database.
     * @param string $value
     * @return boolean
     */
    public function validatePassword($value)
    {
        if (Yii::$app->getSecurity()->validatePassword($value, $this->password)) {
            //echo 'Password matched';
            return true;
        }
        //echo'Wrong password';
        return false;
    }

    public function getAuthKey()
    {
        return $this->token;
    }

    public function getId()
    {
        return $this->id;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new Exception('Not supproted.');
    }

}

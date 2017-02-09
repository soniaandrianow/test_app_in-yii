<?php

namespace app\controllers;

use Yii;
use app\models\Users;

class UsersController extends \yii\web\Controller
{

    /**
     * Registers new user in database.
     * If process successfull -> redirects user to login page.
     * @return mixed
     */
    public function actionRegistration()
    {
        $model = new \app\models\Users();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('conf', 'Account created. Please log in.');
            return $this->redirect(array('users/login'));
        }
        return $this->render('registration', ['model' => $model]);
    }

    /**
     * Allows user to log in.
     * If log in process successfull - redirects user to main page.
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new \app\models\LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user = \app\models\Users::findByUsername($model->username);
            return $this->goBack();
        }
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates existing user's profile.
     * @return type
     */
    public function actionProfile()
    {
        $id = Yii::$app->user->id;
        $user = Users::findOne($id);

        $user->scenario = Users::SCENARIO_UPDATE_ALL;
        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            Yii::$app->session->setFlash('conf', 'Data updated successfully!');
            return $this->redirect(['profile']);
        }
        return $this->render('profile', ['model' => $user]);
    }

}

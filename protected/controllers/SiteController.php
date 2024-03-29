<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	/*public function filters()
    {
        return array(
            array(
                'COutputCache',
                'duration'=>1000,
                'varyByParam'=>array('id'),
            ),
        );
    }*/

	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'

        $this->layout='main';
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
        $this->layout='column0';
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	// public function actionContact()
	// {
 //        $this->layout='column0';
	// 	$model=new ContactForm;
	// 	if(isset($_POST['ContactForm']))
	// 	{
	// 		$model->attributes=$_POST['ContactForm'];
	// 		if($model->validate())
	// 		{
	// 			$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
	// 			$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
	// 			$headers="From: $name <{$model->email}>\r\n".
	// 				"Reply-To: {$model->email}\r\n".
	// 				"MIME-Version: 1.0\r\n".
	// 				"Content-Type: text/plain; charset=UTF-8";

	// 			mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
	// 			Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
	// 			$this->refresh();
	// 		}
	// 	}
	// 	$this->render('contact',array('model'=>$model));
	// }

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if(Yii::app()->user->isGuest)
		{
	        $serviceName = Yii::app()->request->getQuery('service');
	        if (isset($serviceName)) {
	            /** @var $eauth EAuthServiceBase */
	            $eauth = Yii::app()->eauth->getIdentity($serviceName);
	            //$eauth->getItemAttributes();


	            $eauth->redirectUrl = $this->createAbsoluteUrl('/user');
	            $eauth->cancelUrl = $this->createAbsoluteUrl('site/login');

	            try {
	                if ($eauth->authenticate()) {
	                    //var_dump($eauth->getIsAuthenticated(), $eauth->getAttributes());
	                    $identity = new EAuthUserIdentity($eauth);

	                    // successful authentication
	                    if ($identity->authenticate()) {
	                        Yii::app()->user->login($identity);
	                        //var_dump($identity->id, $identity->email, Yii::app()->user->id);exit;
	                         //var_dump($eauth->getIsAuthenticated(), $eauth->getAttributes());exit;
	                        //var_dump($identity);exit;
	                        Yii::app()->session['fbeauth'] = $eauth->getAttributes();
	                        // special redirect with closing popup window
	                       //$eauth->redirect();
	                        $this->redirect(array('/user'));
	                    }
	                    else {
	                        // close popup window and redirect to cancelUrl
	                        $eauth->cancel();
	                    }
	                }

	                // Something went wrong, redirect to login page
	                $this->redirect(array('site/login'));
	            }
	            catch (EAuthException $e) {
	                // save authentication error to session
	                Yii::app()->user->setFlash('error', 'EAuthException: '.$e->getMessage());

	                // close popup window and redirect to cancelUrl
	                $eauth->redirect($eauth->getCancelUrl());
	            }
	        }
	        $this->layout='column0';
	        $this->render('login',array());
	    }else{
	    	$this->redirect(array('/user'));
	    }

	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}

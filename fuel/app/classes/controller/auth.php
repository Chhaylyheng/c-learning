<?php
class Controller_Auth extends Controller
{
	private $_config = null;

	public function before()
	{
		if(!isset($this->_config))
		{
			$this->_config = Config::load('opauth', 'opauth');
		}
	}

	/**
	 * eg. http://www.exemple.org/auth/login/facebook/ will call the facebook opauth strategy.
	 * Check if $provider is a supported strategy.
	 */
	public function action_login($_provider = null,$_mode = null)
	{
		if ($_mode != 'int_callback' && $_mode != 'oauth_callback' && $_mode != 'oauth2callback')
		{
			$sMode = (!is_null($_mode))? $_mode:'TLOGIN';
			Session::set('SES_AUTH_LOGIN_MODE',$sMode);
			Session::set('SES_AUTH_LOGIN_TZ',Input::get('tz',false));
		}

		if(array_key_exists(Inflector::humanize($_provider), Arr::get($this->_config, 'Strategy')))
		{
			$_oauth = new Opauth($this->_config, true);
		}
		else
		{
			Session::set('SES_T_ERROR_MSG','Strategy not supported');
			Response::redirect('index/error/');
		}
	}

	// Print the user credentials after the authentication. Use this information as you need. (Log in, registrer, ...)
	public function action_callback()
	{
		$sMode = Session::get('SES_AUTH_LOGIN_MODE');
		Session::delete('SES_AUTH_LOGIN_MODE');
		$_opauth = new Opauth($this->_config, false);

		switch($_opauth->env['callback_transport'])
		{
			case 'session':
				session_start();
				$response = $_SESSION['opauth'];
				unset($_SESSION['opauth']);
			break;
		}
		if (array_key_exists('error', $response))
		{
			Session::set('SES_T_ERROR_MSG','certification failed.'."\n".'['.$response['error']['message'].']'.$response['error']['raw']['error_reason']);
			Response::redirect('/t/login');
		}
		else
		{
			if (empty($response['auth']) || empty($response['timestamp']) || empty($response['signature']) || empty($response['auth']['provider']) || empty($response['auth']['uid']))
			{
				Session::set('SES_T_ERROR_MSG','certification failed.');
				Response::redirect('index/error/');
			}
			elseif (!$_opauth->validate(sha1(print_r($response['auth'], true)), $response['timestamp'], $response['signature'], $reason))
			{
				Session::set('SES_T_ERROR_MSG','certification failed.'.$reason);
				Response::redirect('index/error/');
			}
			else
			{
				# 成功処理
				switch ($sMode)
				{
					case "TENTRY":
						$aSave = array(
							'provider'=>$response['auth']['provider'],
							'uid'=>$response['auth']['uid'],
							'info'=>$response['auth']['info'],
						);
						Session::delete('SES_AUTH');
						Session::set('SES_AUTH',serialize($aSave));
						$sURL = '/t/entry/socialregist/';
						Response::redirect($sURL);
					break;
					case "TLOGIN":
						Session::set('SES_AUTH_UID',$response['auth']['uid']);
						$sURL = '/t/login/oauthloginchk/'.$response['auth']['provider'].'/';
						Response::redirect($sURL);
					break;
					case "TCONECT":
						$aSave = array(
							'provider'=>$response['auth']['provider'],
							'uid'=>$response['auth']['uid'],
							'info'=>$response['auth']['info'],
						);
						Session::delete('SES_AUTH');
						Session::set('SES_AUTH',serialize($aSave));
						$sURL = '/t/profile/socialconnect/';
						Response::redirect($sURL);
					break;
					case "SENTRY":
					break;
					case "SLOGIN":
					break;
					default:
						Session::set('SES_T_ERROR_MSG',__('認証に失敗しました。').$reason);
						Response::redirect('index/error/');
					break;
				}
			}
		}
	}
}
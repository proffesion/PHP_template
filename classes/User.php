<?php
class User
{
    private $_db,
            $_data,
            $_sessionName,
            $_cookieName,
            $_isLoggedIn = false;

    public function __construct($user = null) {
        $this->_db = DB::getInstance(); // DB

        $this->_sessionName = Config::get('session/session_name'); // assign the session name
        $this->_cookieName  = Config::get('remember/cookie_name'); // assign the session name

        if (!$user) {
            if (Session::exists($this->_sessionName)) {
                $user = Session::get($this->_sessionName);

                if ($this->find($user)) {
                    $this->_isLoggedIn = true;
                } else {
                    // process logout
                }
            }
        } else {
            $this->find($user);
        }
    }

    public function create($fields = array()) {
        if(!$this->_db->insert('users', $fields)) {
            throw new Exception('There was a problem creating an account.');
        }
    }

    public function update($fields = array(), $id = null) {

        if (!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }

        if (!$this->_db->update('users', $id, $fields)) {
            throw new Exception('There was a problem updating');
        }
    }

    public function find($user = null) {
        if ($user) {
            $field = (is_numeric($user)) ? 'id' : 'username';
            $data = $this->_db->get('users', array($field, '=', $user));

            if ($data->count()) {
                $this->_data = $data->first();
                return true;
            }
        }
        return false;
    }

    public function login($username = null, $password = null, $remember = false) { 
        
        if (!$username && !$password && $this->exists()) {
            // log the user in using the (id)
            Session::put($this->_sessionName, $this->data()->id);
        } else {
            # log the user in by the data frrom the forrm
            $user = $this->find($username);
           
            if ($user) {
                if ($this->data()->password === Hash::make($password, $this->data()->salt)) {
                    Session::put($this->_sessionName, $this->data()->id);
    
                    if ($remember) {
                        $hash = Hash::unique(); // generate a hash
                        $hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));
                        
                        if (!$hashCheck->count()) { // check if there is no result from the database cound
                            // insert the hash in the DB
                            $this->_db->insert('users_session', array(
                                'user_id' => $this->data()->id,
                                'hash'    => $hash
                            ));
                        } else {
                            // set the cookie to the one in the DB
                            $hash = $hashCheck->first()->hash;
                        }
    
                        // set the cookie
                        Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
                    }
                    return true;
                }
            }
        }
        
        return false;
    }

    // users permission method
    public function hasPermission($key) {
        $group = $this->_db->get('groups', array('id', '=', $this->data()->groupe));
        
        if ($group->count()) {
            // convert from JSON into an array
            $permissions = json_decode($group->first()->permissions, true); 
            
            if ($permissions[$key] == true) {
                return true;
            }
        }
        return false;
    }

    // check whether the data exists in the data array
    public function exists() {
        return (!empty($this->_data)) ? true : false;
    }

    public function logout() {
        if (!empty($this->data()->id)) {
            $this->_db->delete('users_session', array('user_id', '=', $this->data()->id));
        }

        if (Session::exists($this->_sessionName)) {
            Session::delete($this->_sessionName);
        }
        
        if (Cookie::exists($this->_cookieName)) {
            Cookie::delete($this->_cookieName);
        }
    }

    public function data() {
        return $this->_data;
    }

    public function isLoggedIn() {
        return $this->_isLoggedIn;
    }
}

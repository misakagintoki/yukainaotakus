<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {

    public $helpers = array('Html', 'Form','Flash');
    public $components = array('Flash');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add', 'logout');
    }

    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                $this->Flash->success('登陆成功');
                //debug($_SESSION);
              
                return $this->redirect(array('action' => 'index'));
                //return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error(__('用户名或者密码有误，请重新输入'));
        }
    }   

    public function logout() {
    return $this->redirect($this->Auth->logout());
    }

    public function index($id = null) {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
        $id=$this->Auth->user('id');
       if(isset($id)){
        $this->loadModel('Collection');
        $tmp = $this->Collection->find('list',array('fields'=>'game_id',
        'conditions' => array('user_id'=>$id)));

        foreach($tmp as $value){
            $my[]=$value;
        }
        $this->loadModel('GameInfo');
        $aaa = $this->GameInfo->find('all',array('conditions'=>array('id'=>$tmp)));

      //$my=
        $this->set("list",$aaa);
       }else{echo 123;}

       
        
       

        // $list = $this->Collection->find('all');
		// $this->set("list", $list);
    }






    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('无效数据'));
        }
        $this->set('user', $this->User->findById($id));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Flash->success(__('注册成功'));
                $this->loadModel('UserInfo');
                $newinfo=$this->UserInfo->create(array('nickname'=>'肥大','user_id'=>$this->User->getLastInsertID()));
                $this->UserInfo->save($newinfo);
                $this->Auth->login();
                return $this->redirect(array('action' => 'index'));
            }
            $this->Flash->error(
                __('失败，请重试')
            );
        }
    }
//这里的编辑其实是user_info的内容
    public function edit($id = null) {
        $this->User->id = $id;
        $this->loadModel('UserInfo');
        
        //debug($this->UserInfo->findByUserId($id));
        if (!$this->User->exists()) {
            throw new NotFoundException(__('无效数据'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $now=$this->UserInfo->findByUserId($id);
                $sex=array_sum($_POST['data']['UserInfo']['sex_div']);
                //$result=$this->request->data;
                $data=[
                    'id'=>$now['UserInfo']['id'],
                    'user_id'=>$id,
                    'nickname'=>$_POST['data']['UserInfo']['nickname'],
                    'email'=>$_POST['data']['UserInfo']['email'],
                    'sex_div'=>$sex,
                    'birthday'=>$_POST['data']['UserInfo']['birthday']
                    ];

                   // debug($now['UserInfo']);

            if ($this->UserInfo->save($data)) {
                $this->Flash->success(__('编辑成功'));
                //debug($result);
                return $this->redirect(array('action' => 'index'));
            }
            $this->Flash->error(
                __('失败，请重试')
            );
        } else {
            $this->request->data = $this->UserInfo->findByUserId($id);
            //unset($this->request->data['UserInfo']['id']);
        }
    }

    public function delete($id = null) {


        $this->request->allowMethod('post');

        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Flash->success(__('账号删除'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Flash->error(__('账号删除失败'));
        return $this->redirect(array('action' => 'index'));
    }


    function upload()
{
    if($this->request->data){
        if($this->User->saveAll($this->request->data)){
            echo 'ok';
        }
    }
}

}

?>
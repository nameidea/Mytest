<?php
namespace app\index\controller;
use app\common\model\Teacher;  // 教师模型
use think\facade\Request;   
use think\Controller;   // 请求
/**
 * 教师管理，继承think\Controller后，就可以利用V层对数据进行打包了。
 */
class TeacherController extends IndexController
{
   protected $batchValidate = true;
    public function index()
    {
        // 获取查询信息
        $name = Request::instance()->get('name');

        // 实例化Teacher
        $Teacher = new Teacher; 

        trace($Teacher, 'debug');

        // 按条件查询数据并调用分页
        $teachers = Teacher::where('name', 'like', '%' . $name . '%')->paginate(5); 

        // 向V层传数据
        $this->assign('teachers', $teachers);

        // 取回打包后的数据
        $htmls = $this->fetch();

        // 将数据返回给用户
        return $htmls;
    }

    public function add()
    {
        // 实例化
        $Teacher = new Teacher;

        // 设置默认值
        $Teacher->id = 0;
        $Teacher->name = '';
        $Teacher->username = '';
        $Teacher->sex = 0;
        $Teacher->email = '';
        $this->assign('Teacher', $Teacher);

        // 调用edit模板
        return $this->fetch('edit');
    }
    public function delete()
    {
        try {
            // 获取get数据
            $Request = Request::instance();
            $id = Request::instance()->param('id/d');
            
            // 判断是否成功接收
            if (is_null($id) || 0 === $id) {
                throw new \Exception('未获取到ID信息', 1);
            }

            // 获取要删除的对象
            $Teacher = Teacher::get($id);

            // 要删除的对象存在
            if (is_null($Teacher)) {
                throw new \Exception('不存在id为' . $id . '的教师，删除失败', 1);
            }

            // 删除对象
            if (!$Teacher->delete()) {
                return $this->error('删除失败:' . $Teacher->getError());
            }

        // 获取到ThinkPHP的内置异常时，直接向上抛出，交给ThinkPHP处理
        } catch (\think\Exception\HttpResponseException $e) {
            throw $e;

        // 获取到正常的异常时，输出异常
        } catch (\Exception $e) {
            return $e->getMessage();
        } 

        // 进行跳转
        return $this->success('删除成功', $Request->header('referer')); 
    }


    public function edit()
    {
        try {
            // 获取传入ID
            $id = Request::instance()->param('id/d');

            // 在Teacher表模型中获取当前记录
            if (null === $Teacher = Teacher::get($id))
            {
                // 由于在$this->error抛出了异常，所以也可以省略return(不推荐)
                $this->error('系统未找到ID为' . $id . '的记录');
            } 
            
            // 将数据传给V层
            $this->assign('Teacher', $Teacher);

            // 获取封装好的V层内容
            $htmls = $this->fetch();

            // 将封装好的V层内容返回给用户
            return $htmls;

        // 获取到ThinkPHP的内置异常时，直接向上抛出，交给ThinkPHP处理
        } catch (\think\Exception\HttpResponseException $e) {
            throw $e;

        // 获取到正常的异常时，输出异常
        } catch (\Exception $e) {
            return $e->getMessage();
        } 
    }

    public function update()
    {
     
        // 接收数据，取要更新的关键字信息
        $id = Request::instance()->post('id/d');

        // 获取当前对象
        $Teacher = Teacher::get($id);

        if (!is_null($Teacher)) {
            $result = $this->validate(
      [
   'name' => Request::instance()->post('name'),
   'sex' => Request::instance()->post('sex'),
    'email' => Request::instance()->post('email'),
      ],
      'app\index\validate\Teacher.edit');
          if ( true !== $result) 
          {
            dump($result);
          } 
           
       else {
            // 接收传入数据
            $postData = Request::instance()->post();    

            // 为对象赋值
            $Teacher->name = $postData['name'];
            $Teacher->username = $Teacher->username;
            $Teacher->sex = $postData['sex'];
            $Teacher->email = $postData['email'];
           $Teacher->force()->save();
            // 成功跳转至index触发器
        return $this->success('操作成功', url('index'));
        }
    
       }
    }

 
    public function save()
    {
        // 实例化
        $Teacher = new Teacher;

        $result = $this->validate(
      [
    'name' => Request::instance()->post('name'),
    'username' => Request::instance()->post('username'),
     'sex' => Request::instance()->post('sex'),
    'email' => Request::instance()->post('email'),
      ],
      'app\index\validate\Teacher.add');
      if ( true !== $result) 
          {
            dump($result);
          } 
          else {
             // 接收传入数据
            $postData = Request::instance()->post();    

            // 实例化Teacher空对象
            $Teacher = new Teacher();

            // 为对象赋值
            $Teacher->name = $postData['name'];
            $Teacher->username = $postData['username'];
            $Teacher->sex = $postData['sex'];
            $Teacher->email = $postData['email'];

            // 新增对象至数据表
            $saves = $Teacher->save();
            return $this->success('操作成功', url('index'));
           
          }
            } 
          }        

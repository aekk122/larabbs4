<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 获取 Faker 实例
        $faker = app(Faker\Generator::class);

        // 头像数据
        $avatars = [
        	'https://fsdhubcdn.phphub.org/uploads/images/201710/14/1/s5ehp11z6s.png?imageView2/1/w/200/h/200',
            'https://fsdhubcdn.phphub.org/uploads/images/201710/14/1/Lhd1SHqu86.png?imageView2/1/w/200/h/200',
            'https://fsdhubcdn.phphub.org/uploads/images/201710/14/1/LOnMrqbHJn.png?imageView2/1/w/200/h/200',
            'https://fsdhubcdn.phphub.org/uploads/images/201710/14/1/xAuDMxteQy.png?imageView2/1/w/200/h/200',
            'https://fsdhubcdn.phphub.org/uploads/images/201710/14/1/ZqM7iaP4CR.png?imageView2/1/w/200/h/200',
            'https://fsdhubcdn.phphub.org/uploads/images/201710/14/1/NDnzMutoxX.png?imageView2/1/w/200/h/200',
        ];
        // 生成数据集合
        $users = factory(User::class)->times(10)->make()->each(function ($user, $index) use($avatars, $faker){
        	// 从头像数组中随机取出一个赋值
        	$user->avatar = $faker->randomElement($avatars);
        });

        // 让隐藏字段可见，并将集合转为数组
        $users_array = $users->makeVisible(['password', 'remember_token'])->toArray();

        // 插入
        User::insert($users_array);

        // 单独处理第一个用户
        $user = User::find(1);
        $user->name = 'WENHAO';
        $user->email = "460905539@qq.com";
        $user->password = bcrypt('aekk122');
        $user->avatar = 'https://fsdhubcdn.phphub.org/uploads/images/201710/14/1/ZqM7iaP4CR.png?imageView2/1/w/200/h/200';
        $user->assignRole('Founder');
        $user->save();

        // 给 2 号用户分配管理员权限
        $user = User::find(2);
        $user->assignRole('Maintainer');
    }
}

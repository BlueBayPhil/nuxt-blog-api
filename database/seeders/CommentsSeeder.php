<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $posts = Post::all();

        foreach ($users as $user) {
            for ($i = 0; $i < mt_rand(0, 25); $i++) {
                $post = $posts[$i];
                Comment::factory([
                    'post_id' => $post->id,
                    'user_id' => $user->id
                ])->create();
            }
        }
    }
}

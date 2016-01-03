<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Post;
use SocialService;

class PublishMessage extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $post;

    /**
     * Create a new job instance.
     *
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
      $service = SocialService::get($this->post->service);

      if (filter_var($this->post->message, FILTER_VALIDATE_URL) !== false) {
        $service->postLink($this->post->provider_id, $this->post->message);
      } else {
        $service->postMessage($this->post->provider_id, $this->post->message);
      }
    }
}

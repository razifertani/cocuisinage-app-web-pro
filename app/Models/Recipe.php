<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Recipe extends Model
{

    protected $table = 'recipes';
    protected $fillable  = ['img', 'cov_image', 'default_tag_id', 'creator_id', 'hasImage'];

    protected $appends = [ 'image_url' ,'cov_url' ,'steps', 'tags'];

    /*
     * TODO internationalise string
     */
    public function getStatusString()
    {
        switch ($this->status) {
            case 0:
                return "en cours de création";
            case 1:
                return "active";
            default:
                return "inactive";
        }
    }

    public function defaultTag()
    {
        return $this->belongsTo(Tag::class , 'default_tag_id');
    }

    public function steps()
    {
        return $this->belongsToMany('App\Models\Step', 'recipe_step')->withPivot('position');
    }
    public function parent()
    {
        return $this->belongsTo('App\Models\Recipe', 'parent_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id');
    }


    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag', 'recipe_tag');
    }
    public function acceptableTag()
    {
        return Tag::allRacine()->diff($this->tags()->get());
    }


    public function ingredients()
    {
        $res = Collect();
        $steps =  $this->belongsToMany('App\Models\Step', 'recipe_step')->withPivot('position')->get();

        foreach ($steps as $step) {
            foreach ($step->tags()->get() as $tag) {
                $res->add($tag);
            }
        }
        return $res;
    }

    public function hasImage(): bool
    {
        return $this->hasImage;
    }

    public function getImageUrlAttribute(): string
    {

        if ($this->hasImage) {
            return $this->img;
        }
        else{
            if ($this->img != null){

                return  Storage::disk('s3')->temporaryUrl(
                    "recipe/$this->img",
                    now()->addMinutes(5)
                );
            } else {
                return "https://static.wikia.nocookie.net/minecraft_gamepedia/images/9/9e/Barrier_%28held%29_JE2_BE2.png/revision/latest/scale-to-width-down/150?cb=20200224220440";
            }
        }

    }

     public function getCovUrlAttribute()
    {
            if ($this->cov_image != null){

                return  Storage::disk('s3')->temporaryUrl(
                    "recipe/$this->cov_image",
                    now()->addMinutes(5)
                );
            } else {
                return null;
            }
    }

    public function setImage($image)
    {
        $image->storeAs("recipe", $this->getKey(), 's3');
    }

    public function deleteImage()
    {
        Storage::disk('s3')->delete("recipe/$this->id");
    }

    public function supress()
    {
        $this->deleteImage();
        $this->steps()->detach();
        $this->delete();
    }



    public function getStepsAttribute()
    {
        return $this->steps()->get();
    }

    public function getTagsAttribute()
    {
        return $this->tags()->with('firstParent')->get();
    }

    public function likesUsers()
    {
        return $this->belongsToMany(User::class, 'user_recipe_likes')->withTimestamps();
    }
}

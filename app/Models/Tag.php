<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Tag extends Model
{


    protected $appends = ['image_url'];
    protected $fillable  = ['img', 'parent_id' , 'name' , 'group' , 'is_family' , 'default_product_id'];

    public function getImageUrlAttribute()
    {
        // if ($this->hasImage()){
        //     return $this->img;
        // }else
        // {
        if ($this->img != null) {
            return  Storage::disk('s3')->temporaryUrl(
                "tag/$this->img",
                now()->addWeek(1)
            );
        } else {
            return "https://static.wikia.nocookie.net/minecraft_gamepedia/images/9/9e/Barrier_%28held%29_JE2_BE2.png/revision/latest/scale-to-width-down/150?cb=20200224220440";
        }
        // }
    }
    public static function allRacine()
    {
        return Tag::all()->diff(Tag::whereHas('child')->get());
    }
    public static function noLinked()
    {
        return Tag::doesnthave('parent')->doesnthave('child')->get();
    }

    public static function allFamilies()
    {
        return Tag::where('is_family', 1)->get();
    }

    public function parent()
    {
        return $this->belongsTo(Tag::class, 'parent_id');
    }
    public function firstParent()
    {
        return $this->belongsTo(Tag::class, 'first_parent');
    }
    public function child()
    {
        return $this->hasMany(Tag::class, 'parent_id');
    }
    public function allChildren($list)
    {

        foreach ($this->child()->get()->diff($list) as $tag_child) {
            $list->push($tag_child);
            $tag_child->allChildren($list);
        }
        return $list;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_tags');
    }

    public function steps()
    {
        return $this->belongsToMany(Step::class, 'step_tag')->withPivot('quantity', 'unit');
    }



    public function allProductUnder()
    {
        $tags = $this->allChildren(Collect());


        $products = Collect();

        foreach ($tags as $tag) {
            foreach ($tag->products()->get() as $product) {
                $products->push($product);
            }
        }

        return $products->unique('id');
    }


    // public static function treeHTMLBase($base){
    //     $res = "";

    //     foreach (Tag::allFamilies()->diff(Tag::all()->where('source','!=', $base)) as $_tag){
    //         $res .= Tag::recursiveHTMLTree($_tag,null);
    //     }
    //     return $res;
    // }

    public function hasImage(): bool
    {
        return $this->hasImage;
    }

    public function getImageUrl($duration = 5)
    {
        return $this->img;
        // Storage::disk('s3')->temporaryUrl("tag/$this->id", now()->addMinute($duration));
    }

    public function setImage($image)
    {
        $image->storeAs("tag", $this->getKey(), 's3');
    }

    public function deleteImage()
    {
        Storage::disk('s3')->delete("tag/$this->id");
    }

    public function getImageAttribute()
    {
        if ($this->hasImage()) {
            return $this->getImageUrl();
        } else {
            return "https://static.wikia.nocookie.net/minecraft_gamepedia/images/9/9e/Barrier_%28held%29_JE2_BE2.png/revision/latest/scale-to-width-down/150?cb=20200224220440";
        }
    }
    public function __toString()
    {
        return "Tag id: " . $this->id . "name: " . $this->name;
    }

    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'blog_tag', 'tag_id', 'blog_id');
    }

    public function property()
    {
        return $this->belongsToMany(Property::class, 'tag_proerties')->withPivot('value', 'unit', 'ratio');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function alergene()
    {
        return $this->belongsToMany(Tag::class, 'alergen_tag_tags', 'tag_id');
    }

    public function ingrediantAlergene()
    {
        return $this->belongsToMany(Tag::class, 'alergen_tag_tags', 'alergen_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_tags')->withPivot('qte', 'unit');
    }
    public function unit()
    {
        return $this->belongsToMany(Unit::class, 'tag_unit', 'tag_id', 'unit_id')->withPivot('qte', 'unit' , 'default');
    }
    public function productTag()
    {
        return $this->belongsTo(Product::class, 'menu_tags', 'product_id');
    }

    public function defaultProduct()
    {
        return $this->belongsTo(Product::class, 'default_product_id');
    }
}

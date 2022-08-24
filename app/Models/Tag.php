<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Tag extends Model
{


    protected $appends = ['image_url', 'cov_url'];
    protected $fillable  = ['img', 'parent_id', 'default_product_id', 'group', 'first_parent', 'img_cover',  'name', 'is_family'];

    public static function allRacine()
    {
        return Tag::doesntHave('child')->get();
    }
    public static function noLinked()
    {
        return Tag::doesnthave('parent')->doesnthave('child')->get();
    }

    public static function allFamilies()
    {
        return Tag::where('is_family', 1)->get();
    }
    public function recipe()
    {
        return $this->hasOne(Recipe::class, 'default_tag_id');
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

    public function allChildrenProduct($list)
    {

        foreach ($this->child()->wherehas('products')->get()->diff($list) as $tag_child) {
            $list->push($tag_child);
            $tag_child->allChildrenProduct($list);
        }
        return $list;
    }

    public function allParents($list)
    {

        foreach ($this->parent()->get()->diff($list) as $tag_parent) {
            $list->push($tag_parent);
            $tag_parent->allParents($list);
        }
        return $list;
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'products_tags');
    }

    public function steps()
    {
        return $this->belongsToMany('App\Models\Step', 'step_tag')->withPivot('quantity', 'unit');
    }

    public function acceptableProduct()
    {
        return Product::all()->diff($this->products()->get());
    }

    public function acceptableChildrenTags()
    {
        $tags = tag::where('group', 'lost_tag')->get();

        return $tags;
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



    public function acceptableParentsTags()
    {
        if ($this->group != 'lost_tag') {
            $tags = tag::all()->where('group', '=', $this->group)->merge(tag::all()->where('group', '=', 'lost_tag'));
        } else {
            $tags = tag::all();
        }


        return $tags->diff($this->allParents(collect([$this]))->merge($this->allChildren(collect([]))));
    }

    public static function recursiveHTMLTree($tag, $parent_tag)
    {
        $res = "<li class='tag'>";
        $res .= $tag->name . ' <a type="button" href="/tag/' . $tag->id . '/show"><i class="fas fa-eye"></i> </a>';
        $res .= '<button data-toggle="modal" value="' . $tag->name . '" id="' . $tag->id . '" data-target="#show" type="button" class="border-0 text-success plus-tag-button"><i class="fas fa-plus"></i> </button>';
        if ($parent_tag) {
            $res .= '<button data-toggle="modal" parent_id="' . $parent_tag->id . '" child_id="' . $tag->id . '" data-target="#unlink" type="button" class="border-0 text-danger unlink-tag-button"><i class="fas fa-unlink"></i> </button>';
        }
        if ($tag->child()->count() == 0) {
            $res .= '<button data-toggle="modal" tag_id="' . $tag->id . '" data-target="#addProduct" type="button" class="border-0 text-primary add-product">';
            $res .= '<i class="fas fa-plus-square"></i>';
            $res .= '</button>';
        }
        if ($tag->group == 'ingredient_tag') {
            $res .= '<button data-toggle="modal" tag_id="' . $tag->id . '" data-target="#addAlergene" type="button" class="border-0 text-primary add-alergene">';
            $res .= '<i class="fas fa-notes-medical"></i>';
            $res .= '</button>';
        }

        if ($tag->products()->count() > 0) {
            $res .= '<ul>';
            foreach ($tag->products()->get() as $product) {
                $res .= "<li><a href='/product/show/" . $product->id . "'>" . $product->name . "</a></li>";
            }
            $res .= "</ul>";
        }
        if ($tag->child()->count() > 0) {
            $res .= '<ul>';
            foreach ($tag->child()->get() as $tag_child) {
                $res .= Tag::recursiveHTMLTree($tag_child, $tag);
            }
            $res .= "</ul>";
        }



        $res .= "</li>";
        return $res;
    }
    public static function treeHTML($group)
    {
        $res = "";

        foreach (Tag::where('is_family', 1)->where('group', $group)->get() as $_tag) {
            $res .= Tag::recursiveHTMLTree($_tag, null);
        }
        return $res;
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
        return true;
    }



    public function setImage($image)
    {
        $image->storeAs("tag", $this->getKey(), 's3');
    }

    public function deleteImage()
    {
        Storage::disk('s3')->delete("tag/$this->id");
    }

    public function getImageUrlAttribute()
    {
        // if ($this->hasImage()){
        //     return $this->img;
        // }else
        // {
        if ($this->img != null) {
            return  Storage::disk('s3')->temporaryUrl(
                "tag/$this->img",
                now()->addMinutes(5)
            );
        } else {
            return "https://static.wikia.nocookie.net/minecraft_gamepedia/images/9/9e/Barrier_%28held%29_JE2_BE2.png/revision/latest/scale-to-width-down/150?cb=20200224220440";
        }
        // }
    }
    public function getCovUrlAttribute()
    {

        if ($this->img_cover != null) {
            return  Storage::disk('s3')->temporaryUrl(
                "tag/$this->img_cover",
                now()->addMinutes(5)
            );
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
        return $this->belongsToMany(Unit::class, 'tag_unit', 'tag_id', 'unit_id')->withPivot('qte', 'unit', 'default');
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

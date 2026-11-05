<?php

namespace App\Http\Controllers;

use App\Models\CategoryDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;
use App\Models\ArticleDetail;
use App\Models\CouponDetail;

class CategoryController extends Controller
{
    /**
     *  Method to load category on frontend
     *
     *  @return \Illuminate\Http\JsonResponse
     */
    public function getHomeCategory()
    {
        try {
            $category_data = CategoryDetail::selectRaw("id,name")->where("isDisplayInMore", 1)->where("level", 0)->orderBy("more_section_index")->get()->toArray();
            return json_encode($category_data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     *  Method to load category add form
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function loadCategory(Request $request)
    {
        try {
            $category_data_array = $this->getCategory();
            return view("category.list", compact("category_data_array"));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function getCategory()
    {
        $category_data_array = array();
        $category_data = CategoryDetail::selectRaw("id,name,parentId,level")->where("level", 0)->where("name","!=","All")->orderBy("id")->get();
        foreach (count((array)$category_data) > 0 ? $category_data : array() as $c) {
            $sub_category_array = array();
            $sub_category_data = CategoryDetail::selectRaw("id,name,parentId,level")->where("level", 1)->where("parentId", $c->id)->orderBy("id")->get();
            foreach (count((array)$sub_category_data) > 0 ? $sub_category_data : array() as $sc) {
                $last_category_array = array();
                $last_sub_category_data = CategoryDetail::selectRaw("id,name,parentId,level")->where("level", 2)->where("parentId", $sc->id)->orderBy("id")->get();
                foreach (count((array)$last_sub_category_data) > 0 ? $last_sub_category_data : array() as $lc) {
                    $last_category_array[$lc->id] = array("name" => $lc->name, "parent_id" => $lc->parentId, "level" => $lc->level, "child_list" => array());
                }
                $sub_category_array[$sc->id] = array("name" => $sc->name, "parent_id" => $sc->parentId, "level" => $sc->level, "child_list" => $last_category_array);
            }
            $category_data_array[$c->id] = array("name" => $c->name, "parent_id" => $c->parentId, "level" => $c->level, "child_list" => $sub_category_array);
        }
        return $category_data_array;
    }

    /**
     *  Method to load category add form
     *
     *  @return \Illuminate\Http\JsonResponse
     */

    public function insertCategory(Request $request)
    {
        try {

            $element_array = array(
                'name' => 'required',
            );
            $level = request("level");
            $image = $request->file("iconImage");
            if (isset($image)) {
                $element_array['iconImage'] = 'image|mimes:jpeg,png,jpg,gif,svg';
            }
            if ($level != 0) {
                $element_array['parentId'] = 'required';
            }

            if ($request->input("isDisplayInMore") == "on") {
                $element_array['more_section_index'] = ['required', function ($attribute, $value, $fail) {
                    $parentid = (isset(request("parentId")[0]) ? request("parentId")[0] : 0);
                    $data = CategoryDetail::where("parentId", $parentid)->where("level", request("level"))->where("more_section_index", $value);
                    $category_id = request("category_id");
                    if ($category_id != null) {
                        $data = $data->where("id", "!=", $category_id);
                    }
                    $data = $data->count();
                    if ($data > 0) {
                        return $fail("This index is used by another category");
                    }
                }];
            }
            if ($request->input("isDisplayInMenu") == "on") {
                $element_array['nav_menu_index'] = ['required', function ($attribute, $value, $fail) {
                    $parentid = (isset(request("parentId")[0]) ? request("parentId")[0] : 0);
                    $data = CategoryDetail::where("parentId", $parentid)->where("level", request("level"))->where("nav_menu_index", $value);
                    $category_id = request("category_id");
                    if ($category_id != null) {
                        $data = $data->where("id", "!=", $category_id);
                    }
                    $data = $data->count();
                    if ($data > 0) {
                        return $fail("This index is used by another category");
                    }
                }];
            }
            if ($request->input("isDisplayInFrontend") == "on") {
                $element_array['frontend_menu_index'] = ['required', function ($attribute, $value, $fail) {
                    $parentid = (isset(request("parentId")[0]) ? request("parentId")[0] : 0);
                    $data = CategoryDetail::where("parentId", $parentid)->where("level", request("level"))->where("frontend_menu_index", $value);
                    $category_id = request("category_id");
                    if ($category_id != null) {
                        $data = $data->where("id", "!=", $category_id);
                    }
                    $data = $data->count();
                    if ($data > 0) {
                        return $fail("This index is used by another category");
                    }
                }];
            }
            $validator = Validator::make($request->all(), $element_array, [
                'name.required' => 'Please enter category name',
                'parentId.required' => 'Please select parent category',
                'iconImage.required' => 'Please select icon image',
                'nav_menu_index.required' => 'Please enter menu index',
                'more_section_index.required' => 'Please enter more menu index',
                'frontend_menu_index.required' => 'Please enter frontend menu index',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $category_id = request("category_id");

            if ($category_id == null) {
                $data = new CategoryDetail();
            } else {
                $data = CategoryDetail::where("id", $category_id)->first();
            }
            $data->name = $request->input("name");
            $data->parentId = (isset($request->input("parentId")[0]) ? $request->input("parentId")[0] : 0);
            $data->level = $level;
            $data->isActualCategory = ($request->input("isActualCategory") == "on" ? 1 : 0);
            $data->isDisplayInMore = ($request->input("isDisplayInMore") == "on" ? 1 : 0);
            $data->more_section_index = (($data->isDisplayInMore) ? $request->input("more_section_index") : 0);
            $data->isDisplayInMenu = ($request->input("isDisplayInMenu") == "on" ? 1 : 0);
            $data->nav_menu_index = (($data->isDisplayInMenu) ? $request->input("nav_menu_index") : 0);
            $data->isDisplayInFrontend = ($request->input("isDisplayInFrontend") == "on" ? 1 : 0);
            $data->frontend_menu_index = (($data->isDisplayInFrontend) ? $request->input("frontend_menu_index") : 0);
            $data->isDisplayInApp = ($request->input("isDisplayInApp") == "on" ? 1 : 0);
            $data->isCouponCategory = ($request->input("isCouponCategory") == "on" ? 1 : 0);
            $data->save();

            if (isset($image)) {
                $image_name = $data->id . "." . $image->getClientOriginalExtension();
                $destination_path = public_path("uploads/category");

                if (!File::exists($destination_path)) {
                    File::makeDirectory($destination_path);
                }
                $image->move($destination_path, $image_name);
                $data->iconImage = $image_name;
                $data->save();
            }

            return redirect()->route("category-list")->with("success", "Category ".($category_id == null ? "inserted" : "updated")." successfully");
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function editCategory($id)
    {
        try {
            $category_data = CategoryDetail::where("id", $id)->first();
            if ($category_data) {
                $category_data_array = $this->getCategory();
                return view("category.edit", compact("category_data", "category_data_array"));
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deleteCategory($id)
    {
        try {
            $category_data = CategoryDetail::where("id", $id)->first();
            if ($category_data) {
                if($category_data->isCouponCategory){
                    $coupon_using = CouponDetail::where("categoryId",$id)->count();
                    if($coupon_using > 0){
                        return redirect()->route("category-list")->with("error", "You cannot delete category as it is used by coupons");
                    }
                }

                $article_using_cat = ArticleDetail::where("categoryId",$id)->count();
                if($article_using_cat > 0){
                    return redirect()->route("category-list")->with("error", "You cannot delete category as it is used by articles");
                }
                if ($category_data->iconImage != "" && file_exists(public_path("uploads/category/" . $category_data->iconImage))) {
                   unlink(public_path( "uploads/category/" . $category_data->iconImage));
                }
                $category_data->delete();
                return redirect()->route("category-list")->with("success", "Category deleted successfully");
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}

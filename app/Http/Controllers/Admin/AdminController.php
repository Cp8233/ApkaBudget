<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Models\Transaction;
use Illuminate\Support\Facades\File; 
use App\Models\IdentityType;
use App\Models\Country;
use App\Models\Service;
use App\Models\State;
use App\Models\City;
use App\Models\User;


  /** ============================
             * ✅Dashboard Functionality 
             * ============================ */
class AdminController extends Controller
{
    protected function index()
    {
        return view('Admin.dashboard');
    }


     /** ============================
             * ✅Category Functionality 
             * ============================ */


    protected function categories()
    {
        $categories = Category::all();
        return view('Admin.category.index', compact('categories'));
    }
    protected function add_categories(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'category' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048' 
            ]);
    
            if ($validator->fails()) {
                if ($request->json()) {
                    return response()->json([
                        'status' => 'false',
                        'message' => $validator->errors()
                    ], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }
    
            $categories = new Category();
            $categories->category = $request->category;
    
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/categories'), $imageName);
                $categories->image = 'uploads/categories/' . $imageName;
            }
            $categories->save();
    
            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Category added successfully',
                    'route' => route('admin.categories') 
                ], 200);
            }                           
        }
        return view('Admin.category.add');
    }
    
    protected function edit_categories(Request $request, $id)
    {
        $category = Category::find($id);
    
      
        if (!$category) {
            return redirect()->route('admin.add_categories')->with('error', 'Category not found.');
        }
    
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'category' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'false',
                    'message' => $validator->errors()
                ], 422);
            }
    
            $category->category = $request->category;
    
            if ($request->hasFile('image')) {
                if ($category->image && file_exists(public_path($category->image))) {
                    unlink(public_path($category->image));
                }
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/categories'), $imageName);
                $category->image = 'uploads/categories/' . $imageName;
            }
    
            $category->save();
    
            if ($request->json()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Category updated successfully',
                    'route' => route('admin.categories'),
                ], 200);
            }   
        }
        return view('Admin.category.edit', compact('category'));
    }
    protected function delete_categories($id){
        $user=Category::findOrFail($id);
        $user->delete();
   
        return response()->json([
           'status' => 1, 
           'message' => 'Data Delete successfully', 
           'route' => route('admin.categories'),
        ], 200);
      }

    /** ============================
             * ✅subcategoty Functionality 
             * ============================ */

   protected function subcategories($CategoryId)
             {
                 $data = Subcategory::where('category_id', $CategoryId)->get();
                 return view('Admin.subcategory.index', compact('data', 'CategoryId'));
             }
    protected function add_subcategories(Request $request, $CategoryId)
    {   
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'false',
                    'message' => $validator->errors()
                ], 422);
            }
    
            $subcategory = new SubCategory();
            $subcategory->category_id = $CategoryId;
            $subcategory->name = $request->name;
    
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = 'image_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/categories/'), $fileName);
                $subcategory->image ='uploads/categories/'. $fileName;
            }
    
            $subcategory->save();
            return redirect()->route('admin.subcategories',['CategoryId' => $CategoryId])->with('success', 'Subcategory added successfully.');
        }
    
        return view('Admin.subcategory.add', ['CategoryId' => $CategoryId]);
    }

    protected function edit_subcategories(Request $request, $CategoryId, $id)
    {   
        $subcategory = SubCategory::findOrFail($id);
    
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name'  => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'false',
                    'message' => $validator->errors()
                ], 422);
            }
    
            $subcategory->category_id = $CategoryId;
            $subcategory->name = $request->input('name');
    
            if ($request->hasFile('image')) {
                if ($subcategory->image && file_exists(public_path($subcategory->image))) {
                    unlink(public_path($subcategory->image));
                }
    
                $file = $request->file('image');
                $fileName = 'image_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/categories/'), $fileName);
                $subcategory->image = 'uploads/categories/' . $fileName;
            }
    
            $subcategory->save();
    
            return redirect()->route('admin.subcategories', ['CategoryId' => $CategoryId])
                ->with('success', 'Subcategory edited successfully.');
        }
    
        return view('Admin.subcategory.edit', compact('CategoryId', 'subcategory'));
    }
    protected function delete_subcategories($CategoryId, $id)
    {
        try {
            $subcategory = SubCategory::findOrFail($id); 
            if ($subcategory->image && file_exists(public_path($subcategory->image))) {
                unlink(public_path($subcategory->image));
            }
            $subcategory->delete(); 
             return response()->json([
                'status' => 1,
                'message' => 'Subcategory deleted successfully!',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete subcategory!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

      /** ============================
             * ✅Contory data fetch on cities  Functionality 
             * ============================ */
    protected function countries()
    {
        $data = Country::all();
        return view('Admin.country.index', compact('data'));
    }

      /** ============================
             * ✅Users  Functionality 
             * ============================ */
      protected function users()
       {
           $users = User::where('role', 1)->orderBy('id','DESC')->get();
            return view('Admin.users.index', compact('users'));
       }

      protected function add_users(Request $request)
      {
          if ($request->isMethod('post')) {
              $validator = Validator::make($request->all(), [
                'name' => 'required|regex:/^[A-Za-z\s]+$/|max:255',
               'mobile_no' => 'required|numeric|digits:10|unique:users,mobile_no',
                'email' => 'required|email|unique:users,email', 
              ]);

          if ($validator->fails()) {
               return response()->json([
                  'status' => 0,
                  'errors' => $validator->errors(), 
                 ], 422);
    }

        $user = new User();
        $user->name      = $request->name;
        $user->mobile_no = $request->mobile_no;
        $user->email     = $request->email;
        $user->role      = 1; 
        $user->save();

        return response()->json([
            'status' => 1, 
            'message' => 'Data Added successfully', 
            'route' => route('admin.users'),
          ], 200);
        }
        else
        {
            return view('Admin.users.add');
        }
     }

    protected function edit_users(Request $request, $id)
    {
        if ($request->isMethod('post')) {
        $request->validate([
            'name' => 'required|regex:/^[A-Za-z\s]+$/|max:255',
            'mobile_no' => 'required|numeric|digits:10|unique:users,mobile_no',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);
    
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->mobile_no = $request->mobile_no;
        $user->email = $request->email;
        $user->save();
    
        return response()->json([
            'status' => 1, 
            'message' => 'Data Edit successfully', 
            'route' => route('admin.users'),
         ], 200);
    }else{
        $data = User::findOrFail($id);
        return view('Admin.users.edit',compact('data'));
    }
    }
    
   protected function delete_users($id){
     $user=User::findOrFail($id);
     $user->delete();

     return response()->json([
        'status' => 1, 
        'message' => 'Data Delete successfully', 
        'route' => route('admin.users'),
     ], 200);
   }

     /** ============================
             * ✅Providers Functionality 
             * ============================ */
   protected function providers()
   {
       $providers=User::where('role',2)->orderBy('id','DESC')->get();
        return view('Admin.providers.index', compact('providers'));
   }
   protected function add_providers(Request $request)
   {
       if ($request->isMethod('post')) {
           $validator = Validator::make($request->all(), [
               'name' => 'required|regex:/^[A-Za-z\s]+$/|max:255',
               'mobile_no' => 'required|numeric|digits:10|unique:users,mobile_no',
               'email' => 'required|email|unique:users,email',
               'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
               'password' => 'required|string|min:6',
               'country_id' => 'required|exists:countries,id', 
               'state_id' => 'required|exists:states,id',
               'city_id' => 'required|exists:cities,id',
               'pincode' => 'required|digits:6',
               'address' => 'required|string|max:500',
               'category_id' => 'required|exists:categories,id',
               'experience' => 'required|integer|min:0',
               'identity_id' => 'required|integer',
               'identity_number' => 'required|string|max:50',
               'identity_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
           ]);

           if ($validator->fails()) {
               return response()->json([
                   'status' => 0,
                   'errors' => $validator->errors(),
               ], 422);
           }

           $user = new User();
           $user->name = $request->name;
           $user->mobile_no = $request->mobile_no;
           $user->email = $request->email;
           $user->role = 2;
           $user->password = Hash::make($request->password);
           $user->country_id = $request->country_id;
           $user->state_id = $request->state_id;
           $user->city_id = $request->city_id;
           $user->pincode = $request->pincode;
           $user->address = $request->address;
           $user->category_id = $request->category_id;
           $user->experience = $request->experience;
           $user->identity_id = $request->identity_id;
           $user->identity_number = $request->identity_number;
           $user->identity_image = $request->identity_image;

           if ($request->hasFile('profile')) {
               $file = $request->file('profile');
               $filename = 'profile_' . time() . '.' . $file->getClientOriginalExtension();
               $file->move(public_path('uploads/profiles/'), $filename);
               $user->profile = $filename;
           }
           if ($request->hasFile('identity_image')) {
            $file = $request->file('identity_image');
            $filename = 'identity_image' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/identities/'), $filename);
            $user->identity_image = $filename;
        }
           $user->save();
           return response()->json([
               'status' => 1,
               'message' => 'Provider Added Successfully',
               'route' => route('admin.providers'),
           ], 200);
       }
       $countries =Country::all(); 
       $categories = Category::all();
       $identities = IdentityType::all();

       return view('Admin.providers.add', compact('countries', 'categories','identities'));
   }
   protected function getStates($country_id)
   {
       $states = State::where('country_id', $country_id)->get();
       return response()->json($states);
   }
   
   protected function getCities($state_id)
   {
       $cities = City::where('state_id', $state_id)->get();
       if ($cities->isEmpty()) {
           return response()->json(['message' => 'Cities not found'], 404);
       }
       return response()->json($cities);
   }


   protected function edit_providers(Request $request, $id)
   {
      $provider = User::findOrFail($id);

      if ($request->isMethod('post'))
       {
         try {
             $request->validate([
               'mobile_no' => 'required|numeric|digits:10|unique:users,mobile_no',
                'email' => 'required|email|unique:users,email,' . $id,
                'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'password' => 'nullable|string|min:6',
                'country_id' => 'required|integer',
                'state_id' => 'required|integer',
                'pincode' => 'required|digits:6',
                'address' => 'required|string|max:500',
                'category_id' => 'required|integer',
                'experience' => 'required|integer|min:0',
                'identity_id' => 'required|integer',
                'identity_number' => 'required|string|max:50',
                'identity_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $provider->name = $request->name;
            $provider->mobile_no = $request->mobile_no;
            $provider->email = $request->email;

            if ($request->hasFile('profile')) {
                $file = $request->file('profile');
                $filename = 'profile_'.time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/profiles'), $fileName);
                $provider->profile = $fileName;
            }
            
            if ($request->hasFile('identity_image')) {
                $file = $request->file('identity_image');
                $fileName = 'identity_image_'.time() . '_'. $file->getClientOriginalName();
                $file->move(public_path('uploads/identities'), $fileName);
                $provider->identity_image = $fileName;
            }
            if ($request->password) {
                $provider->password = Hash::make($request->password);
            }
            $provider->country_id = $request->country_id;
            $provider->state_id = $request->state_id;
            $provider->pincode = $request->pincode;
            $provider->address = $request->address;
            $provider->category_id = $request->category_id;
            $provider->experience = $request->experience;
            $provider->identity_id = $request->identity_id;
            $provider->identity_number = $request->identity_number; 
            $provider->identity_image = $request->identity_image;
            $provider->save();

            return response()->json([
                'status' => 1,
                'message' => 'Provider updated successfully',
                'route' => route('admin.providers'),
            ], 200);

         }
          catch (\Exception $e) 
          {
            return response()->json
            ([
                'status' => 'error',
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ], 500);
         }
      }

        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        $categories = Category::all();
        $identities = IdentityType::all();

        return view('Admin.providers.edit', compact('provider', 'countries', 'states', 'cities', 'categories', 'identities'));
        }

        protected function delete_providers($id){
            $user=User::findOrFail($id);
            $user->delete();
       
            return response()->json([
               'status' => 1, 
               'message' => 'Data Delete successfully', 
               'route' => route('admin.providers'),
            ], 200);
          }
            /** ============================
             * ✅sub subCategory Functionality 
             * ============================ */
            protected function subSubCategory($subcategory_id)
            
                {
                    $subcategory = SubCategory::find($subcategory_id);
                
                    if (!$subcategory) {
                        return redirect()->back()->with('error', 'Subcategory not found!');
                    }
                
                    $subSubCategories = SubSubCategory::with('subcategory.category')
                        ->where('subcategory_id', $subcategory->id)
                        ->get();
                
                    return view('Admin.subsubcategory.index', [
                        'subcategory_id' => $subcategory_id,
                        'subcategory_name' => $subcategory->subcategory_name,
                        'subSubCategories' => $subSubCategories
                    ]);
                }

            protected function addSubSubCategory(Request $request, $subcategory_id)
            {
                $subcategory = Subcategory::find($subcategory_id);
            
                if (!$subcategory) {
                    return redirect()->back()->with('error', 'Subcategory not found!');
                }
            
                if ($request->isMethod('get')) {
                    $subSubCategories = SubSubCategory::where('subcategory_id', $subcategory->id)->get();
            
                    return view('Admin.subsubcategory.add', compact('subcategory', 'subSubCategories', 'subcategory_id'));
                }
            
                // Validation
                $validator = Validator::make($request->all(), [
                    'sub_subcategory_name' => 'required|string|max:255',
                    'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
                ]);
            
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            
                // Image Upload
                $imagePath = null;
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $fileName = 'image_' . time() . '-' . $file->getClientOriginalName();
                    $filePath = public_path('uploads/categories');
                    $file->move($filePath, $fileName);
                    $imagePath = 'uploads/categories/' . $fileName;
                }
            
                // Data Insert
                SubSubCategory::create([
                    'subcategory_id' => $subcategory_id,
                    'sub_subcategory_id' => $subcategory_id,
                    'sub_subcategory_name' => $request->sub_subcategory_name,
                    'image' => $imagePath,
                ]);
            
                return redirect()->route('admin.subSubCategories', ['subcategory_id' => $subcategory->id]);
            }
            
  protected function editSubSubCategory(Request $request, $subcategory_id, $id)
      {
         
        $subSubCategory = SubSubCategory::find($id);
             if (!$subSubCategory) {
             return redirect()->back()->with('error', 'Sub-Sub Category not found.');
           }

        if ($request->isMethod('get')) {
             $subcategory = SubCategory::find($subcategory_id);

               return view('Admin.subsubcategory.edit', [
               'subSubCategory' => $subSubCategory,
               'subcategory_id' => $subcategory_id
               ]);
           }
      
         
          if ($request->isMethod('post')) {
              $subcategory = SubCategory::find($subcategory_id);
              $subSubCategory = SubSubCategory::find($id);
      
              if (!$subSubCategory) {
                  return redirect()->back()->with('error', 'Sub-Sub Category not found.');
              }
      
            
              $request->validate([
                  'sub_subcategory_name' => 'required|string|max:255',
                  'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
              ]);
      
          
              if ($request->hasFile('image')) {
                
                  if ($subSubCategory->image && File::exists(public_path($subSubCategory->image))) {
                      File::delete(public_path($subSubCategory->image));
                  }
      
                  $file = $request->file('image');
                  $fileName = 'image_' . time() . '-' . $file->getClientOriginalName();
                  $filePath = public_path('uploads/categories');
                  $file->move($filePath, $fileName);
                  $subSubCategory->image = 'uploads/categories/' . $fileName;
              }
              $subSubCategory->sub_subcategory_name = $request->sub_subcategory_name;
              $subSubCategory->save();
      
              return redirect()->route('admin.subSubCategories', ['subcategory_id' => $subcategory->id]);
          }
          return redirect()->back()->with('error', 'Invalid request method.');
      }


      
  protected function deleteSubSubCategory($subcategory_id, $id)
      {
          $subSubCategory = SubSubCategory::where('subcategory_id', $subcategory_id)->where('id', $id)->first();
      
          if (!$subSubCategory) {
              return response()->json([
                'status' => 1,
                 'message' => 'Sub-Sub Category not found!'], 404);
          }
      
          if ($subSubCategory->image && file_exists(public_path($subSubCategory->image))) {
              unlink(public_path($subSubCategory->image));
          }
      
          $subSubCategory->delete();
      
          return response()->json([
            'status' => 1,
            'message' => 'Sub-Sub Category deleted successfully!',
            'route'=>route('admin.subSubCategories',compact('subcategory_id','id'))
            ]);
      }
      


            /** ============================
             * ✅ServiceList Functionality 
             * ============================ */   
    
             protected function service($category_id, $subcategory_id, $id)
             {
                 $services = Service::where('category_id', $category_id)
                     ->where('subcategory_id', $subcategory_id)
                     ->where('sub_subcategory_id', $id)
                     ->get();
             
                 return view('Admin.service.index', compact('services', 'category_id', 'subcategory_id', 'id'));
             }
             
            
                // Add Service
                protected function addService(Request $request, $category_id, $subcategory_id, $sub_subcategory_id)
                {
                    if ($request->isMethod('get')) {
                        return view('Admin.service.add', [
                            'category_id' => $category_id,
                            'subcategory_id' => $subcategory_id,
                            'sub_subcategory_id' => $sub_subcategory_id
                        ]);
                    }
                
                    $request->validate([
                        'service_name' => 'required|string|max:255',
                        'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        'price'        => 'required|numeric',
                        'time'         => 'required|string|max:255'
                    ]);
                
                    $service = new Service();
                    $service->category_id    = $category_id;
                    $service->subcategory_id = $subcategory_id;
                    $service->sub_subcategory_id = $sub_subcategory_id;
                    $service->service_name   = $request->service_name;
                    $service->price          = $request->price;
                    $service->time           = $request->time;
                
                    if ($request->hasFile('image')) {
                        $file = $request->file('image');
                        $fileName = 'image_' . time() . '.' . $file->getClientOriginalExtension();
                        $filePath = public_path('uploads/services');
                        $file->move($filePath, $fileName);
                        $service->image = 'uploads/services/' . $fileName;
                    }
                
                    $service->save();
                
                    return redirect()->route('admin.service', ['category_id' => $category_id, 'subcategory_id' => $subcategory_id, 'id' => $sub_subcategory_id])->with('success', 'Service Added Successfully!');
                }
                
                // Edit Service
                protected function editService(Request $request, $category_id, $subcategory_id, $sub_subcategory_id, $service_id)
                   {
                   
                     $subsubcategory = Service::find($service_id);

                      if (!$subsubcategory) {
                      return response()->json([
                        'status' => 0,
                        'message' => 'Service Not Found!'
                        ]);
                       }

         if ($request->isMethod('get')) {
             return view('Admin.service.edit', compact('subsubcategory', 'category_id', 'subcategory_id', 'service_id'));
             }
            
          $request->validate([
             'service_name' => 'required|string|max:255',
             'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
             'price'        => 'required|numeric',
             'time'         => 'required'
           ]);

         $subsubcategory->service_name = $request->service_name;
         $subsubcategory->price        = $request->price;
         $subsubcategory->time         = $request->time;

         if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = 'image_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = public_path('uploads/services');

         if ($subsubcategory->image && File::exists(public_path($subsubcategory->image))) {
            File::delete(public_path($subsubcategory->image));
          }

           $file->move($filePath, $fileName);
           $subsubcategory->image = 'uploads/services/' . $fileName;
         }
         $subsubcategory->save();
        

            return redirect()->route('admin.service', [
                'category_id' => $category_id,
                'subcategory_id' => $subcategory_id,
                'id' => $sub_subcategory_id
            ])->with('success', 'Service Updated Successfully!');
}

           
protected function deleteService($id)
{
    $service = Service::find($id);

    if (!$service) {
        return response()->json([
            'status' => 0,
            'message' => 'Service Not Found!'
        ]);
    }

    // Purani Image Delete
    if ($service->image && file_exists(public_path($service->image))) {
        unlink(public_path($service->image));
    }

    $service->delete();

    return response()->json([
        'status' => 1,
        'message' => 'Service deleted successfully!',
        'route' => route('admin.service', [
            'category_id' => $service->category_id,
            'subcategory_id' => $service->subcategory_id,
            'id' => $service->id 
        ])
    ]);
}

                
    

              /** ============================
             * ✅Transaction Functionality 
             * ============================ */  
        
    protected function transaction(){
        $transactions = Transaction::all();
        return view('Admin.transaction.index',compact('transactions'));
    }

    protected function TransProvider()
    {
        $providers = User::with(['subscriptions.plan'])
        ->where('role', 2)
        ->get();
    
        return view('Admin.transaction.transProvider', compact('providers'));
    }
    
}

    
            

    
    

            

        

                




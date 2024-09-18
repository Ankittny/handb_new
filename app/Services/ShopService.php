<?php

namespace App\Services;

use App\Traits\FileManagerTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
class ShopService
{
    use FileManagerTrait;
    /**
     * @param object $vendor
     * @return array
     */
    public function getShopDataForAdd(object $vendor):array
    {
        return [
            'seller_id' =>$vendor['id'],
            'name' => $vendor['f_name'],
            'address' => '',
            'contact' => $vendor['phone'],
            'image' => 'def.png',
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    /**
     * @return array[name: mixed, address: mixed, contact: mixed, image: bool|mixed, banner: bool|mixed, bottomBanner: bool|mixed, offerBanner: bool|mixed]
     */
    public function getShopDataForUpdate(object $request , object $shop):array
    {
        $storage = config('filesystems.disks.default') ?? 'public';
        //$this->CreateWarehouse($request, $vendorId, $storage);
        $image = $request['image'] ? $this->update(dir:'shop/', oldImage: $shop['image'], format: 'webp',image:  $request->file('image')) : $shop['image'];
        $banner = $request['banner'] ? $this->update(dir: 'shop/banner/',oldImage:  $shop['banner'], format: 'webp',image:  $request->file('banner')): $shop['banner'];
        $bottomBanner = $request['bottom_banner'] ? $this->update(dir: 'shop/banner/', oldImage: $shop['bottom_banner'], format: 'webp', image: $request->file('bottom_banner')) : $shop['bottom_banner'];
        $offerBanner = $request['offer_banner'] ? $this->update(dir: 'shop/banner/', oldImage: $shop['offer_banner'], format: 'webp',image:  $request->file('offer_banner')) : $shop['offer_banner'];
        return [
            'name'=>$request['name'],
            'address'=>$request['address'],
            'contact'=>$request['contact'],
          	'city' => $request['city'],
            'state' => $request['state'],
            'country' => $request['country'],
            'pin_code' => $request['pin_code'],
            'image'=> $image,
            'image_storage_type' => $request->has('image') ? $storage : $shop['image_storage_type'],
            'banner'=> $banner,
            'banner_storage_type'=> $request->has('banner') ? $storage : $shop['banner_storage_type'],
            'bottom_banner'=> $bottomBanner,
            'bottom_banner_storage_type'=> $request->has('bottom_banner') ? $storage : $shop['bottom_banner_storage_type'],
            'offer_banner'=> $offerBanner,
            'offer_banner_storage_type'=> $request->has('offer_banner') ? $storage : $shop['offer_banner_storage_type'],
        ];
    }
  
  
  

    /**
     * @return array[vacation_status: int, vacation_start_date: mixed, vacation_end_date: mixed, vacation_note: mixed]
     */
    public function getVacationData(object $request):array
    {
        return [
            'vacation_status' => $request['vacation_status'] == 'on' ? 1 : 0,
            'vacation_start_date' => $request['vacation_start_date'],
            'vacation_end_date' => $request['vacation_end_date'],
            'vacation_note' => $request['vacation_note'],
        ];
    }
    public function getAddShopDataForRegistration(object $request,int $vendorId):array
    {
        $storage = config('filesystems.disks.default') ?? 'public';
       	$this->CreateWarehouse($request, $vendorId, $storage);
        return [
            'seller_id' => $vendorId,
            'name' => $request['shop_name'],
            'slug' => Str::slug($request['shop_name'], '-') . '-' . Str::random(6),
            'address'=>$request['shop_address'],
            'contact' => $request['phone'],
            'image' => $this->upload(dir: 'shop/', format: 'webp', image: $request->file('logo')),
            'image_storage_type' => $request->has('logo') ? $storage : null,
            'banner' => $this->upload(dir: 'shop/banner/', format: 'webp', image: $request->file('banner')),
            'banner_storage_type' =>$request->has('banner') ? $storage : null,
            'bottom_banner' => $this->upload(dir: 'shop/banner/', format: 'webp', image: $request->file('bottom_banner')),
            'bottom_banner_storage_type' =>$request->has('banner') ? $storage : null,
        ];
    }
  
  
  public function CreateWarehouse(object $request, int $vendorId, string $storage):void{
        $client = new Client();
        $response = $client->post('https://track.delhivery.com/api/backend/clientwarehouse/create/', [
            'json' => [
                'name' => $request['shop_name'],
                'email' => $request['email'],
                'phone' => $request['phone'],
                'address' => $request['shop_address'],
                'city' => $request['city'],
                'country' => $request['country'],
                'pin' => $request['pin_code'],
                'return_address' => $request['shop_address'],
                'return_pin' => $request['pin_code'],
                'return_city' => $request['city'],
                'return_state' => $request['state'],
                'return_country' => $request['country'],
            ],
            'headers' => [
                'Authorization' => 'Token 298946431eb6b00835b0cf6aaaad8c9a4242c111',
                'Accept' => 'application/json',
            ],
            'verify' => false,
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
    }

}

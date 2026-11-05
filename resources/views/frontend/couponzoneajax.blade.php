@if(count($coupons)!="")
<div class="row mt-5">
    <?php

    $numOfCols = 4;
    $rowCount = 0;
    $bootstrapColWidth = 12 / $numOfCols;
    $bootstrapColWidth1 = $bootstrapColWidth * 2;
    $bootstrapColWidth2 = 12;
    ?>

    <?php
    foreach ($coupons as $coupon) {
        ?>

        <div class="mb-5 col-lg-<?php echo $bootstrapColWidth; ?> col-md-<?php echo $bootstrapColWidth1; ?>">
            <div class="couponzone-inner" onclick="window.location.href='{!!url('/coupondetail').'/'.$coupon->id!!}'">
                <img src="{{ "../".($coupon->thumbnailImage)}}" alt="{!! $coupon->heading !!}">
                <div class="coupon-detail">
                    <div class="head_name">
                        <p class="coupon_head text-left">{!! $coupon->heading !!}</p>
                        <p class="company_name text-left">{!! $coupon->companyName!!}</p>
                    </div>
                    <p class="price"><span class="Original_Price">C${!! $coupon->originalPrice !!}</span> &nbsp;<span class="Discount_Price">C${!! $coupon->discountPrice !!}</span></p>
                    <?php
                    $less =  $coupon->originalPrice - $coupon->discountPrice;
                    $discount_price = (100 * $less) / $coupon->originalPrice;
                    $offerDetail = (empty($coupon->offerDetail) ? (round($discount_price) . "% OFF") : $coupon->offerDetail);
                    ?>
                    <div><span class="discount-box">{!!$offerDetail!!}</span></div>
                </div>
            </div>
        </div>



        <?php
        $rowCount++;
        if ($rowCount % $numOfCols == 0) echo '</div> <div class="row partition">';
    }
    ?>
</div>
@endif

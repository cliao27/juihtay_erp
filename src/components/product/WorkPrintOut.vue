<template>
  <div v-if="appDocument.order">
    <section id="work-printout-header">
      <b-row>
        <b-col>รหัสสินค้า</b-col>
        <b-col>
          <b-row>{{appDocument.pcns}}</b-row>
          <b-row>{{ appDocument.product.ext_ref}}</b-row>
        </b-col>
        <b-col>
          <b-row class="text-primary">ใบปฏิบัติงาน</b-row>
          <b-row>
            <barcode
            :value="'PN'+appDocument.ptn"
            width="1"
            height="12"
            displayValue="false"
            textmargin="0"
            format="code39"
          >Error Rendering</barcode>
          </b-row>
        </b-col>
        <b-col>
          <b-row>
            <b-col>เลขที่</b-col>
            <b-col>{{appDocument.jwn}}</b-col>
          </b-row>
          <b-row>
            <b-col>วันที่</b-col>
            <b-col>{{appDocument.entry_datetime}}</b-col>
          </b-row>
        </b-col>
      </b-row>
    </section>
    <section id="product-information">
      <b-row>
        <b-col class="text-sm-right small" cols="1">รายการสินค้า</b-col>
        <b-col class="border rounded" cols="6">{{appDocument.product.product_name}}</b-col>
        <b-col class="text-sm-right small">จำนวน</b-col>
        <b-col class="border rounded">{{appDocument.order_qty}}</b-col>
        <b-col class="text-sm-right small">วันที่ส่งของ</b-col>
        <b-col class="border rounded">{{appDocument.order_due}}</b-col>
      </b-row>
      <b-row>
        <b-col class="text-sm-right small" cols="1">ชนิดกระดาษ</b-col>
        <b-col class="border rounded" cols="6">{{appDocument.product.paper_code}}</b-col>
        <b-col class="text-sm-right small">จำนวนกระดาษ</b-col>
        <b-col class="border rounded">{{appDocument.work_qty}} {{appDocument.print_length}}</b-col>
        <b-col class="text-sm-right small">PO.NO.</b-col>
        <b-col class="border rounded">{{appDocument.order.order_number}}</b-col>
      </b-row>
    </section>
    <section id="raw-material-information" >
      <b-row class="border border-success">
        <b-col cols="1" class="text-sm-right small">การตัด</b-col>
        <b-col>
          <b-row>
            <b-col class="border rounded">Raw Matterial</b-col>
            <b-col class="border rounded">{{appDocument.product.paper_code}}</b-col>
            <b-col class="border rounded">Roll</b-col>
            <b-col class="border rounded">170 mm</b-col>
            <b-col class="border rounded">{{appDocument.print_length}} M</b-col>
          </b-row>
          <b-row>
            <b-col class="border rounded">Printing</b-col>
            <b-col class="border rounded">{{appDocument.product.print_type}}</b-col>
            <b-col class="border rounded">Roll</b-col>
            <b-col
              class="border rounded"
            >{{appDocument.product.product_width}} x {{appDocument.product.product_length}}</b-col>
            <b-col class="border rounded">{{print_qty}}</b-col>
          </b-row>
          <b-row>
            <b-col class="border rounded">Product</b-col>
            <b-col class="border rounded">{{appDocument.pcns}}</b-col>
            <b-col class="border rounded">{{appDocument.qty_unit}}</b-col>
            <b-col class="border rounded"></b-col>
            <b-col class="border rounded">{{appDocument.work_qty}}</b-col>
          </b-row>
        </b-col>
      </b-row>
    </section>
    <section id="raw-material-information-old">
      <b-row>
        <b-col cols="1" class="text-sm-right small">การตัด</b-col>
        <b-col>
          <b-row>
            <b-col class="border rounded">--</b-col>
            <b-col class="border rounded">รายละเอียดในการตัด</b-col>
            <b-col class="border rounded">ชิ้น</b-col>
            <b-col class="border rounded">จำนวนชิ้น</b-col>
            <b-col class="border rounded">วันที่ / ลงชื่อ</b-col>
          </b-row>
          <b-row>
            <b-col class="border rounded">{{appDocument.product.cuts}}</b-col>
            <b-col
              class="border rounded"
            >{{appDocument.product.product_width}} x {{appDocument.product.product_length}}</b-col>
            <b-col class="border rounded">{{appDocument.product.prints}}</b-col>
            <b-col class="border rounded">{{appDocument.print_qty}}</b-col>
            <b-col class="border rounded">--</b-col>
          </b-row>
        </b-col>
      </b-row>
    </section>
    <section id="printing-sequence">
      <b-row class="border border-primary">
        <b-col class="text-sm-center small" cols="5">ลำดับ / (สีที่กำหนด) / รหัสสี</b-col>
        <b-col class="text-sm-right small">ผู้ตรวจสี Q.C</b-col>
        <b-col class="text-sm-right small">NO</b-col>
        <b-col class="text-sm-right small">สูญเสีย</b-col>
        <b-col class="text-sm-right small">คงเหลือ</b-col>
        <b-col class="text-sm-right small">เวลา</b-col>
        <b-col class="text-sm-right small">ลงชื่อ</b-col>
      </b-row>
      <b-row v-for="color in appDocument.product.printing_seq" :key="color.seq">
        <b-col
          class="border rounded"
          cols="5"
        >{{color.seq}}. {{color.ink}} #{{color.mesh}} D:{{color.ruling}} A:{{color.angle}} ID:{{color.stencil_id}}</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
      </b-row>
    </section>
    <section id="product-laminate-information">
      <b-row class="border border-success">
        <b-col class="text-sm-right small">เคลือบ</b-col>
        <b-col class="border rounded">{{appDocument.product.laminate}}</b-col>
        <b-col class="text-sm-right small">ชนิดผ้าเคลือบ</b-col>
        <b-col class="border rounded">{{appDocument.product.tape_code}}</b-col>
        <b-col class="text-sm-right small">สูญเสีย</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
      </b-row>
    </section>
    <section id="product-dicut-information">
      <b-row class="border border-info">
        <b-col class="text-sm-right small">ไดคัท</b-col>
        <b-col class="border rounded">{{appDocument.product.dicut}}</b-col>
        <b-col class="text-sm-right small">รหัสไดคัท</b-col>
        <b-col class="border rounded">{{appDocument.product.dicut_plate}}</b-col>
        <b-col class="text-sm-right small">สูญเสีย</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
      </b-row>
    </section>
    <section id="production-information">
      <b-row>
        <b-col class="text-sm-right small">จำนวนที่ผลิต</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="text-sm-right small">สูญเสียรวม</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="text-sm-right small">รหัสตระกร้า</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
      </b-row>
    </section>
    <section id="production-information">
      <b-row>
        <b-col class="text-sm-right small">จำนวนที่ผลิต</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="text-sm-right small">สูญเสียรวม</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="text-sm-right small">รหัสตระกร้า</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
      </b-row>
    </section>
    <section id="production-information2">
      <b-row>
        <b-col class="text-sm-right small">หมายเหตุ</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="text-sm-right small">ยอดเข้าสต็อก</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="text-sm-right small">สต็อกคงเหลือ</b-col>
        <b-col class="border rounded">-</b-col>
      </b-row>
    </section>
    <section id="production-information2">
      <b-row>
        <b-col class="border rounded">หมายเหตุ</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">ยอดเข้าสต็อก</b-col>
        <b-col class="border rounded">-</b-col>
        <b-col class="border rounded">สต็อกคงเหลือ</b-col>
        <b-col class="border rounded">{{appDocument.stock_out}} / {{appDocument.stock_in}}</b-col>
      </b-row>
    </section>
    <section id="production-information2b">
      <b-row>
        <b-col class="border rounded" cols="10">{{appDocument.print_type}}</b-col>
        <b-col class="border rounded" cols="2">
          <barcode
            :value="appDocument.jwn"
            width="1"
            height="12"
            displayValue="false"
            textmargin="0"
            format="code39"
          >Error Rendering</barcode>
        </b-col>
      </b-row>
    </section>
    <section id="production-information3">
      <b-row>
        <b-col class="border rounded" cols="10">
          <pre>{{appDocument.product.note}}</pre>
        </b-col>
        <b-col class="border rounded">History</b-col>
      </b-row>
    </section>
    <section id="production-information3">
      <b-row>
        <b-col class="border rounded" cols="9">
          <barcode
            :value="appDocument.product.EAN"
            width="1"
            height="12"
            textmargin="0"
            format="upc"
          >Error Rendering</barcode>
          {{appDocument.work_note}}
        </b-col>
        <b-col class="text-sm-right small" cols="3">
          <barcode
            :value="appDocument.passcode"
            width="1"
            height="12"
            displayValue="false"
            textmargin="0"
            format="code128"
          >Error Rendering</barcode>
        </b-col>
      </b-row>
    </section>
    <section id="data-entry-information">
      <b-row>
        <b-col class="text-sm-right small">
          <qrcode-vue :value="appDocument._id" size="50" level="H"></qrcode-vue>
        </b-col>
        <b-col class="text-sm-right small">ผู้สั่งงาน</b-col>
        <b-col class="border rounded">{{appDocument.entry}}</b-col>
        <b-col class="text-sm-right small">ผู้อนุมัติ</b-col>
        <b-col class="border rounded">{{appDocument.verify}}</b-col>
        <b-col class="text-sm-right small">ผู้ตรวจสอบ</b-col>
        <b-col class="border rounded">--</b-col>
        <b-col class="border rounded">{{appDocument.work_progress}}</b-col>
      </b-row>
    </section>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
// import { store } from "@/store/store";
const namespaced = "work";

import VueBarcode from "vue-barcode";
import QrcodeVue from "qrcode.vue";

export default {
  components: {
    barcode: VueBarcode,
    QrcodeVue
  },
  data() {
    return {
      product:[],
      order:[]
    };
  },
  created() {
    // store.appDocument();
    // console.log(store.appDocument.product);
  },
  computed: {
    ...mapGetters(namespaced, ["appDocument"]),
    print_qty(){
        return this.appDocument.work_qty / this.appDocument.product.prints;
    },
    // ...mapGetters("product", ["appDocument"])
  }
};
</script>

<style lang="scss" scoped>
</style>
<template>
  <div>
    <b-input-group>
      <b-form-group
        id="fieldset-1"
        description="รหัสสินค้า 產品編號"
        :label="`${product}-${code}`"
        label-for="input-1"
        :invalid-feedback="invalidFeedback"
        :valid-feedback="validFeedback"
        :state="state"
      >
        <b-form-input id="input-1" v-model="code" :state="state" trim></b-form-input>
      </b-form-group>

      <b-form-group
        id="fieldset-1"
        description="Spec"
        label="-"
        label-for="input-1"
        :invalid-feedback="invalidFeedback"
        :valid-feedback="validFeedback"
        :state="state"
      >
        <b-form-input id="input-1" v-model="name" :state="state" trim></b-form-input>
      </b-form-group>

      <b-form-group
        id="fieldset-1"
        description="จำนวน 訂單數量"
        label="QTY"
        label-for="input-1"
        :invalid-feedback="invalidFeedback"
        :valid-feedback="validFeedback"
        :state="state"
      >
        <b-form-input id="input-1" small v-model="name" :state="state" trim></b-form-input>
      </b-form-group>
      <b-form-group
        id="fieldset-1"
        :description="due_date"
        label="กำหนดส่ง出貨日期"
        label-for="input-1"
        :invalid-feedback="invalidFeedback"
        :valid-feedback="validFeedback"
        :state="state"
      >
        <b-form-datepicker
          id="example-datepicker"
          v-model="due_date"
          :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
          locale="th"
          class="mb-2"
        ></b-form-datepicker>
      </b-form-group>

      <b-form-group
        id="fieldset-1"
        :description="selected"
        label="Type"
        label-for="input-1"
        :invalid-feedback="invalidFeedback"
        :valid-feedback="validFeedback"
        :state="state"
      >
        <b-form-select v-model="selected" :options="options" class="mb-2"></b-form-select>
      </b-form-group>
    </b-input-group>
  </div>
</template>

<script>
export default {
  props: {},
  data() {
    return {
      due_date: "",
      selected: null,
      options: [
        { value: null, text: "Please select an option" },
        { value: "a", text: "This is First option" },
        { value: "b", text: "Selected Option" },
        { value: { C: "3PO" }, text: "This is an option with object value" },
        { value: "d", text: "This one is disabled", disabled: true }
      ]
    };
  }
};
</script>

<style lang="scss" scoped>
</style>
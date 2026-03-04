<template>
  <div class="row g-4">
    <div class="col-12 col-xl-6">
      <div class="card border-0 shadow-sm p-3 p-md-4">
        <div class="placeholder-glow">
          <span class="placeholder col-12" style="height: 320px"></span>
        </div>
        <div class="d-flex gap-2 mt-3 placeholder-glow">
          <span class="placeholder col-2" style="height: 56px"></span>
          <span class="placeholder col-2" style="height: 56px"></span>
          <span class="placeholder col-2" style="height: 56px"></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-6">
      <div class="card border-0 shadow-sm p-3 p-md-4 placeholder-glow">
        <span class="placeholder col-4 mb-3"></span>
        <span class="placeholder col-8 mb-2"></span>
        <span class="placeholder col-5 mb-4"></span>
        <span class="placeholder col-3 mb-3" style="height: 34px"></span>
        <span class="placeholder col-12 mb-2"></span>
        <span class="placeholder col-10"></span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProductDetailSkeleton",
};
</script>

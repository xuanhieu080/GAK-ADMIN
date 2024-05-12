<template>
  <div class="md:flex md:justify-between block overflow-x-auto w-full">
    <p class="pl-0 py-2 rounded my-2 text-xs xs:text-sm text-gray-900">
      Hiển thị từ {{ (currentPage - 1) * perPage + 1 }} đến
      {{ currentPage * perPage > total ? total : currentPage * perPage }} của {{ total }} kết quả
    </p>

    <ul v-if="totalPage > 1" class="flex pl-0 list-none rounded my-2 w-full overflow-x-auto pb-1 md:w-auto">
      <li class="leading-tight bg-white border border-r-0 border-gray-300 border-r-0 ml-0 rounded-l hover:bg-gray-400"
          :class="{'bg-gray-200': isInFirstPage}">
        <button type="button"
                class="py-2 px-3"
                :class="{'cursor-not-allowed': isInFirstPage}"
                :disabled="isInFirstPage"
                @click="gotoFirst">
          &laquo;
        </button>
      </li>

      <li class="relative inline-flex items-center border border-r-0 border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-100 focus:z-20"
          :class="{'bg-gray-200': isInFirstPage}">
        <button type="button"
                class="py-2 px-3"
                :class="{'cursor-not-allowed': isInFirstPage}"
                :disabled="isInFirstPage"
                @click="gotoPrevious">
          &lsaquo;
        </button>
      </li>

      <template v-if="showDots('left')">
        <li class="relative inline-flex items-center border border-r-0 border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-100 focus:z-20"
            :class="{'bg-gray-600': isInFirstPage}">
          <button type="button"
                  class="py-2 px-3"
                  :class="{'cursor-not-allowed': isInFirstPage}"
                  :disabled="isInFirstPage"
                  @click="gotoPageNumber(1)">
            1
          </button>
        </li>

        <li class="relative inline-flex items-center border border-r-0 border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-100 focus:z-20">
          <button type="button"
                  class="py-2 px-3"
                  :disabled="true">
            ...
          </button>
        </li>
      </template>

      <li class="relative inline-flex items-center border border-r-0 border-gray-300 text-sm font-medium text-gray-500 hover:bg-gray-100 focus:z-20"
          v-for="(page, index) in pages"
          :key="`pages_${index}`"
          :class="{'bg-gray-300 hover:text-gray-700': page === currentPage}">
        <button type="button"
                class="py-2 px-3"
                :class="{'cursor-not-allowed': page === currentPage}"
                :disabled="page === currentPage"
                @click="gotoPageNumber(page)">
          {{ page }}
        </button>
      </li>

      <template v-if="showDots('right')">
        <li class="relative inline-flex items-center border border-r-0 border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-100 focus:z-20">
          <button type="button"
                  class="py-2 px-3"
                  :disabled="true">
            ...
          </button>
        </li>

        <li class="relative inline-flex items-center border border-r-0 border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-100 focus:z-20"
            :class="{'bg-gray-600': isInLastPage}">
          <button type="button"
                  class="py-2 px-3"
                  :class="{'cursor-not-allowed': isInLastPage}"
                  :disabled="isInLastPage"
                  @click="gotoPageNumber(totalPage)">
            {{ totalPage }}
          </button>
        </li>
      </template>

      <li class="relative inline-flex items-center border border-r-0 border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-100 focus:z-20"
          :class="{'bg-gray-200': isInLastPage}">
        <button type="button"
                class="py-2 px-3"
                :class="{'cursor-not-allowed': isInLastPage}"
                :disabled="isInLastPage"
                @click="gotoNext">
          &rsaquo;
        </button>
      </li>

      <li class="leading-tight bg-white border border-gray-300 rounded-r border-r hover:bg-gray-400"
          :class="{'bg-gray-200': isInLastPage}">
        <button type="button"
                class="py-2 px-3"
                :class="{'cursor-not-allowed': isInLastPage}"
                :disabled="isInLastPage"
                @click="gotoLast">
          &raquo;
        </button>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: "Pagination",
  props: {
    currentPage: {type: Number, required: true, default: 1},
    pagination: {type: Object, required: true, default: () => ({})},
    maxVisibleButtons: {type: Number, required: false, default: 5},
  },

  data: () => ({
    perPage: 10,
    total: 0,
    totalPage: 0,
    maxVisible: 5,
  }),

  watch: {
    pagination: {
      handler(pagination) {
        this.perPage = pagination.per_page || 10
        this.total = pagination.total || 0
        this.totalPage = pagination.last_page || 0
        if (this.maxVisibleButtons > pagination.last_page) {
          this.maxVisible = pagination.last_page
        } else {
          this.maxVisible = 5
        }
      },
      immediate: true,
    },
  },

  computed: {
    isInFirstPage() {
      return this.currentPage === 1
    },

    isInLastPage() {
      return this.currentPage === this.totalPage
    },

    pages() {
      const range = []

      for (let i = this.startPage; i <= this.endPage; i += 1) {
        range.push(i)
      }

      return range
    },

    startPage() {
      if (this.currentPage === 1) {
        return 1
      }

      if (this.currentPage === this.totalPage) {
        return this.totalPage - this.maxVisible + 1
      }

      return this.currentPage - 1
    },

    endPage() {
      return Math.min(this.startPage + this.maxVisible - 1, this.totalPage)
    },
  },

  methods: {
    showDots(position = "left") {
      const number = position === "left" ? 1 : this.totalPage
      const nextNumber = position === "left" ? 2 : this.totalPage - 1

      return !this.pages.includes(number) || !this.pages.includes(nextNumber)
    },

    gotoFirst() {
      this.gotoPageNumber(1)
    },

    gotoLast() {
      this.gotoPageNumber(this.totalPage)
    },

    gotoPrevious() {
      this.gotoPageNumber(this.currentPage - 1)
    },

    gotoNext() {
      this.gotoPageNumber(this.currentPage + 1)
    },

    gotoPageNumber(pageNumber) {
      this.$emit("changed", pageNumber)
    },
  },
}
</script>

<style scoped>

</style>
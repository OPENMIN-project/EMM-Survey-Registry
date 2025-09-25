<template>
  <default-field :field="field" :errors="errors" :show-help-text="showHelpText">
    <template slot="field">
      <ul class="content-list">
        <li v-for="{id, label, el} in headings" :key="id">
          <span class="cursor-pointer" :href="`#${id}`" @click="el.scrollIntoView()" v-text="label"></span>
        </li>
      </ul>
      <button ref="backToTop" class="hidden focus:outline-none text-white bg-primary opacity-50 px-3 py-2 rounded"
              style="position: fixed; right: 50px; bottom: 60px" @click.prevent="scrollToTop()">Back to top
      </button>
    </template>
  </default-field>
</template>

<script>
import {FormField, HandlesValidationErrors, BehavesAsPanel} from 'laravel-nova'
import {addPreviousButton, addNextButton} from "../navButtons";

const scrollContainer = () => {
  return document.documentElement || document.body;
}

export default {
  data: () => ({
    headings: []
  }),

  mixins: [FormField, HandlesValidationErrors, BehavesAsPanel],

  props: ['resourceName', 'resourceId', 'field'],

  mounted() {
    const cards = Array.from(document.querySelectorAll('div.card')).slice(1)

    const headings = cards.map(card => {
      const heading = card.previousElementSibling
      if (heading && heading.innerText) {
        heading.id = heading.innerText.replaceAll(' ', '_')
        this.headings = [...this.headings, {id: heading.id, label: heading.innerText, el: heading}]
        return heading;
      }
      return null
    }).filter(Boolean)

    headings.forEach((heading, index, list) => {
      switch (index) {
        case 0:
          addNextButton(heading, list[index + 1])
          break;
        case list.length - 1:
          addPreviousButton(heading, list[index - 1])
          break;
        default:
          addPreviousButton(heading, list[index - 1])
          addNextButton(heading, list[index + 1])
      }
    })

    const createAndAddAnotherBtn = document.querySelector('button[dusk="create-and-add-another-button"]')
    const createBtn = document.querySelector('button[dusk="create-button"]')

    createAndAddAnotherBtn && (createAndAddAnotherBtn.innerText = " Save & Add Another Survey ")
    createBtn && (createBtn.innerText = " Save Survey ")

    document.addEventListener("scroll", this.toggleScrollToTop, {passive: true})
  },
  beforeDestroy() {
    document.removeEventListener("scroll", this.toggleScrollToTop, {passive: true})
  },
  methods: {
    toggleScrollToTop() {
      if (scrollContainer().scrollTop > 400) {
        this.$refs.backToTop.classList.remove("hidden")
      } else {
        this.$refs.backToTop.classList.add("hidden")
      }
    },
    fill(formData) {
    },
    scrollToTop() {
      document.body.scrollIntoView({
        behavior: "smooth",
      });
    }
  }
}
</script>

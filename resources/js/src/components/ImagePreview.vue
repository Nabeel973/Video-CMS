<template>
  <div v-if="imageData && imageData.url" class="image-preview-container">
    <label class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
    </label>
    <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
      <div class="flex items-center space-x-4">
        <!-- Image Preview -->
        <div class="flex-shrink-0">
          <img 
            :src="imageData.url" 
            :alt="imageData.name || 'Advertisement Image'"
            class="h-20 w-20 object-cover rounded-lg border border-gray-200"
            @error="handleImageError"
          />
        </div>
        
        <!-- Image Info and Actions -->
        <div class="flex-1">
          <p class="text-sm font-medium text-gray-900">
            Current Image
          </p>
          <p class="text-sm text-gray-500">
            {{ imageData.name || 'Advertisement Image' }}
          </p>
          
          <!-- Action Buttons -->
          <div class="mt-2 flex space-x-2">
            <!-- View Full Size -->
            <button
              type="button"
              @click="viewFullSize"
              class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
              View
            </button>
            
            <!-- Download -->
            <button
              type="button"
              @click="downloadImage"
              class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Download
            </button>
          </div>
        </div>
      </div>
      
      <!-- Replace Image Note -->
      <div class="mt-3 p-2 bg-blue-50 border border-blue-200 rounded">
        <p class="text-xs text-blue-600">
          <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>
          Upload a new image below to replace the current one.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  imageData: {
    type: Object,
    default: null
  },
  label: {
    type: String,
    default: 'Current Image'
  }
})

const emit = defineEmits(['view', 'download'])

const handleImageError = (event) => {
  console.error('Failed to load image:', event.target.src)
  event.target.src = '/placeholder-image.png' // Add a placeholder image
}

const viewFullSize = () => {
  if (props.imageData?.url) {
    window.open(props.imageData.url, '_blank')
  }
}

const downloadImage = () => {
  if (props.imageData?.url) {
    const link = document.createElement('a')
    link.href = props.imageData.url
    link.download = props.imageData.name || 'advertisement-image'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }
}
</script>
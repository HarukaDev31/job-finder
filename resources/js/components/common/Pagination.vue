<template>
  <div class="pagination-container" v-if="pagination && pagination.total > 0">
    <div class="pagination-info">
      <span class="text-muted">
        Mostrando {{ pagination.from }} a {{ pagination.to }} de {{ pagination.total }} resultados
      </span>
    </div>
    
    <nav aria-label="Navegación de páginas">
      <ul class="pagination justify-content-center">
        <!-- Botón Primera Página -->
        <li class="page-item" :class="{ disabled: !pagination.has_previous_page }">
          <a 
            class="page-link" 
            href="#" 
            @click.prevent="goToPage(1)"
            :class="{ 'disabled-link': !pagination.has_previous_page }"
          >
            <i class="fas fa-angle-double-left"></i>
          </a>
        </li>

        <!-- Botón Página Anterior -->
        <li class="page-item" :class="{ disabled: !pagination.has_previous_page }">
          <a 
            class="page-link" 
            href="#" 
            @click.prevent="goToPage(pagination.current_page - 1)"
            :class="{ 'disabled-link': !pagination.has_previous_page }"
          >
            <i class="fas fa-angle-left"></i>
          </a>
        </li>

        <!-- Números de Página -->
        <li 
          v-for="page in visiblePages" 
          :key="page"
          class="page-item"
          :class="{ active: page === pagination.current_page }"
        >
          <a 
            class="page-link" 
            href="#" 
            @click.prevent="goToPage(page)"
          >
            {{ page }}
          </a>
        </li>

        <!-- Botón Página Siguiente -->
        <li class="page-item" :class="{ disabled: !pagination.has_next_page }">
          <a 
            class="page-link" 
            href="#" 
            @click.prevent="goToPage(pagination.current_page + 1)"
            :class="{ 'disabled-link': !pagination.has_next_page }"
          >
            <i class="fas fa-angle-right"></i>
          </a>
        </li>

        <!-- Botón Última Página -->
        <li class="page-item" :class="{ disabled: !pagination.has_next_page }">
          <a 
            class="page-link" 
            href="#" 
            @click.prevent="goToPage(pagination.last_page)"
            :class="{ 'disabled-link': !pagination.has_next_page }"
          >
            <i class="fas fa-angle-double-right"></i>
          </a>
        </li>
      </ul>
    </nav>

    <!-- Selector de elementos por página -->
    <div class="per-page-selector">
      <label for="per-page" class="form-label">Elementos por página:</label>
      <select 
        id="per-page" 
        class="form-select form-select-sm" 
        v-model="perPage"
        @change="changePerPage"
        style="width: auto; display: inline-block; margin-left: 10px;"
      >
        <option value="10">10</option>
        <option value="15">15</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Pagination',
  props: {
    pagination: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      perPage: this.pagination?.per_page || 15
    }
  },
  computed: {
    visiblePages() {
      if (!this.pagination) return [];
      
      const current = this.pagination.current_page;
      const last = this.pagination.last_page;
      const delta = 2; // Número de páginas a mostrar antes y después de la actual
      
      let start = Math.max(1, current - delta);
      let end = Math.min(last, current + delta);
      
      // Ajustar para mostrar siempre 5 páginas si es posible
      if (end - start < 4) {
        if (start === 1) {
          end = Math.min(last, start + 4);
        } else {
          start = Math.max(1, end - 4);
        }
      }
      
      const pages = [];
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    }
  },
  methods: {
    goToPage(page) {
      if (page >= 1 && page <= this.pagination.last_page && page !== this.pagination.current_page) {
        this.$emit('page-changed', page);
      }
    },
    changePerPage() {
      this.$emit('per-page-changed', parseInt(this.perPage));
    }
  },
  watch: {
    pagination: {
      handler(newPagination) {
        if (newPagination) {
          this.perPage = newPagination.per_page;
        }
      },
      immediate: true
    }
  }
}
</script>

<style scoped>
.pagination-container {
  margin-top: 2rem;
  padding: 1rem 0;
  border-top: 1px solid #e9ecef;
}

.pagination-info {
  text-align: center;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

.pagination {
  margin-bottom: 1rem;
}

.page-link {
  color: #007bff;
  border: 1px solid #dee2e6;
  padding: 0.5rem 0.75rem;
  transition: all 0.2s ease;
}

.page-link:hover {
  color: #0056b3;
  background-color: #e9ecef;
  border-color: #dee2e6;
  text-decoration: none;
}

.page-item.active .page-link {
  background-color: #007bff;
  border-color: #007bff;
  color: white;
}

.page-item.disabled .page-link {
  color: #6c757d;
  pointer-events: none;
  background-color: #fff;
  border-color: #dee2e6;
}

.disabled-link {
  cursor: not-allowed;
  opacity: 0.6;
}

.per-page-selector {
  text-align: center;
  font-size: 0.9rem;
}

.form-label {
  margin-bottom: 0;
  margin-right: 0.5rem;
}

.form-select-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
  border-radius: 0.2rem;
}
</style> 
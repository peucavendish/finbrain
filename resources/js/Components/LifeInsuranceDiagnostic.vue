<template>
  <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Diagnóstico de Seguro de Vida</h2>
    
    <form @submit.prevent="submitDiagnostic" class="space-y-6">
      <!-- Dados Pessoais -->
      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-700">Dados Pessoais</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Idade</label>
            <input 
              v-model="formData.age" 
              type="number" 
              min="18" 
              max="100"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Ocupação</label>
            <input 
              v-model="formData.occupation" 
              type="text"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Renda Anual (R$)</label>
            <input 
              v-model="formData.income" 
              type="number" 
              min="0"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            >
          </div>
        </div>
      </div>

      <!-- Condições de Saúde -->
      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-700">Condições de Saúde</h3>
        <div class="space-y-2">
          <div v-for="(condition, index) in formData.health_conditions" :key="index" class="flex items-center gap-2">
            <input 
              v-model="formData.health_conditions[index]"
              type="text"
              class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              placeholder="Digite uma condição de saúde"
            >
            <button 
              type="button" 
              @click="removeHealthCondition(index)"
              class="text-red-500 hover:text-red-700"
            >
              Remover
            </button>
          </div>
          <button 
            type="button" 
            @click="addHealthCondition"
            class="text-blue-500 hover:text-blue-700"
          >
            + Adicionar Condição
          </button>
        </div>
      </div>

      <!-- Fatores de Estilo de Vida -->
      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-700">Fatores de Estilo de Vida</h3>
        <div class="space-y-2">
          <div v-for="(factor, index) in formData.lifestyle_factors" :key="index" class="flex items-center gap-2">
            <input 
              v-model="formData.lifestyle_factors[index]"
              type="text"
              class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              placeholder="Digite um fator de estilo de vida"
            >
            <button 
              type="button" 
              @click="removeLifestyleFactor(index)"
              class="text-red-500 hover:text-red-700"
            >
              Remover
            </button>
          </div>
          <button 
            type="button" 
            @click="addLifestyleFactor"
            class="text-blue-500 hover:text-blue-700"
          >
            + Adicionar Fator
          </button>
        </div>
      </div>

      <!-- Histórico Familiar -->
      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-700">Histórico Familiar</h3>
        <div class="space-y-2">
          <div v-for="(history, index) in formData.family_history" :key="index" class="flex items-center gap-2">
            <input 
              v-model="formData.family_history[index]"
              type="text"
              class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              placeholder="Digite uma condição do histórico familiar"
            >
            <button 
              type="button" 
              @click="removeFamilyHistory(index)"
              class="text-red-500 hover:text-red-700"
            >
              Remover
            </button>
          </div>
          <button 
            type="button" 
            @click="addFamilyHistory"
            class="text-blue-500 hover:text-blue-700"
          >
            + Adicionar Histórico
          </button>
        </div>
      </div>

      <!-- Botão de Envio -->
      <div class="flex justify-end">
        <button 
          type="submit" 
          :disabled="isLoading"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
        >
          {{ isLoading ? 'Analisando...' : 'Realizar Diagnóstico' }}
        </button>
      </div>
    </form>

    <!-- Resultado da Análise -->
    <div v-if="analysisResult" class="mt-8 space-y-6">
      <h3 class="text-xl font-bold text-gray-800">Resultado da Análise</h3>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 bg-gray-50 rounded-lg">
          <h4 class="text-sm font-medium text-gray-500">Score de Risco</h4>
          <p class="text-2xl font-bold" :class="getRiskScoreClass">
            {{ analysisResult.risk_score }}%
          </p>
        </div>
        
        <div class="p-4 bg-gray-50 rounded-lg">
          <h4 class="text-sm font-medium text-gray-500">Cobertura Sugerida</h4>
          <p class="text-2xl font-bold text-gray-800">
            R$ {{ formatCurrency(analysisResult.suggested_coverage) }}
          </p>
        </div>
        
        <div class="p-4 bg-gray-50 rounded-lg">
          <h4 class="text-sm font-medium text-gray-500">Prêmio Mensal Estimado</h4>
          <p class="text-2xl font-bold text-gray-800">
            R$ {{ formatCurrency(analysisResult.monthly_premium_estimate) }}
          </p>
        </div>
      </div>

      <div class="space-y-4">
        <div>
          <h4 class="text-lg font-semibold text-gray-700">Análise Detalhada</h4>
          <p class="mt-2 text-gray-600 whitespace-pre-line">{{ analysisResult.recommendation }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const formData = ref({
  age: '',
  occupation: '',
  income: '',
  health_conditions: [''],
  lifestyle_factors: [''],
  family_history: ['']
});

const isLoading = ref(false);
const analysisResult = ref(null);

const getRiskScoreClass = computed(() => {
  if (!analysisResult.value) return '';
  const score = analysisResult.value.risk_score;
  if (score <= 30) return 'text-green-600';
  if (score <= 70) return 'text-yellow-600';
  return 'text-red-600';
});

const addHealthCondition = () => {
  formData.value.health_conditions.push('');
};

const removeHealthCondition = (index) => {
  formData.value.health_conditions.splice(index, 1);
};

const addLifestyleFactor = () => {
  formData.value.lifestyle_factors.push('');
};

const removeLifestyleFactor = (index) => {
  formData.value.lifestyle_factors.splice(index, 1);
};

const addFamilyHistory = () => {
  formData.value.family_history.push('');
};

const removeFamilyHistory = (index) => {
  formData.value.family_history.splice(index, 1);
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value);
};

const submitDiagnostic = async () => {
  try {
    isLoading.value = true;
    
    // Limpar campos vazios
    const cleanData = {
      ...formData.value,
      health_conditions: formData.value.health_conditions.filter(Boolean),
      lifestyle_factors: formData.value.lifestyle_factors.filter(Boolean),
      family_history: formData.value.family_history.filter(Boolean)
    };

    const response = await axios.post('/api/ai-analysis', cleanData);
    analysisResult.value = response.data.data;
  } catch (error) {
    console.error('Erro ao realizar análise:', error);
    alert('Ocorreu um erro ao realizar a análise. Por favor, tente novamente.');
  } finally {
    isLoading.value = false;
  }
};
</script> 
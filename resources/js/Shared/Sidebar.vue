<template>
  <div>
    <TransitionRoot as="template" :show="sidebarOpen">
      <Dialog class="relative z-50 lg:hidden" @close="sidebarOpen = false">
        <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="" leave="transition-opacity ease-linear duration-300" leave-from="" leave-to="opacity-0">
          <div class="fixed inset-0 bg-gray-900/80" />
        </TransitionChild>

        <div class="fixed inset-0 flex">
          <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
            <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
              <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="" leave="ease-in-out duration-300" leave-from="" leave-to="opacity-0">
                <div class="absolute top-0 left-full flex w-16 justify-center pt-5">
                  <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                    <span class="sr-only">Close sidebar</span>
                    <XMarkIcon class="size-6 text-white" aria-hidden="true" />
                  </button>
                </div>
              </TransitionChild>

              <!-- Mobile Sidebar -->
              <div class="relative flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-2 dark:bg-gray-900 dark:ring dark:ring-white/10 dark:before:pointer-events-none dark:before:absolute dark:before:inset-0 dark:before:bg-black/10">
                <div class="relative flex h-16 shrink-0 items-center">
                  <div class="flex items-center gap-x-3">
                    <Logo />
                    <span class="text-xl font-bold text-gray-900 dark:text-white">YakTrack</span>
                  </div>
                </div>
                <nav class="relative flex flex-1 flex-col">
                  <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                      <ul role="list" class="-mx-2 space-y-1">
                        <li v-for="item in navigation" :key="item.name">
                          <Link 
                            :href="route(item.route)" 
                            :class="[item.current ? 'bg-gray-50 text-indigo-600 dark:bg-white/5 dark:text-white' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white', 'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold']"
                          >
                            <component :is="item.icon" :class="[item.current ? 'text-indigo-600 dark:text-white' : 'text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white', 'size-6 shrink-0']" aria-hidden="true" />
                            {{ item.name }}
                          </Link>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </nav>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Static sidebar for desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col dark:bg-gray-900">
      <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 dark:border-white/10 dark:bg-black/10">
        <div class="flex h-16 shrink-0 items-center">
          <div class="flex items-center gap-x-3">
            <Logo />
            <span class="text-xl font-bold text-gray-900 dark:text-white">YakTrack</span>
          </div>
        </div>
        <nav class="flex flex-1 flex-col">
          <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
              <ul role="list" class="-mx-2 space-y-1">
                <li v-for="item in navigation" :key="item.name">
                  <Link 
                    :href="route(item.route)" 
                    :class="[item.current ? 'bg-gray-50 text-indigo-600 dark:bg-white/5 dark:text-white' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white', 'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold']"
                  >
                    <component :is="item.icon" :class="[item.current ? 'text-indigo-600 dark:text-white' : 'text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white', 'size-6 shrink-0']" aria-hidden="true" />
                    {{ item.name }}
                  </Link>
                </li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import Logo from '@/Shared/Logo.vue'
import {
  Bars3Icon,
  CalendarIcon,
  ChartPieIcon,
  DocumentDuplicateIcon,
  FolderIcon,
  HomeIcon,
  UsersIcon,
  XMarkIcon,
  ClockIcon,
  TagIcon,
  DocumentCheckIcon,
  BriefcaseIcon,
  PuzzlePieceIcon,
  ClipboardDocumentCheckIcon,
  BeakerIcon,
  ViewfinderCircleIcon,
  DocumentTextIcon,
  UserGroupIcon,
  ChartBarIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  open: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:open'])

const sidebarOpen = ref(props.open)

// Watch for prop changes and update local state
watch(() => props.open, (newValue) => {
  sidebarOpen.value = newValue
})

// Watch for local state changes and emit to parent
watch(sidebarOpen, (newValue) => {
  if (newValue !== props.open) {
    emit('update:open', newValue)
  }
})

const navigation = [
  { name: 'Home', route: 'home', icon: HomeIcon, current: route().current('home') },
  { name: 'Sessions', route: 'session.index', icon: ClockIcon, current: route().current('session.*') },
  { name: 'Categories', route: 'session-category.index', icon: TagIcon, current: route().current('session-category.*') },
  { name: 'Tasks', route: 'task.index', icon: DocumentCheckIcon, current: route().current('task.*') },
  { name: 'Sprints', route: 'sprint.index', icon: CalendarIcon, current: route().current('sprint.*') },
  { name: 'Projects', route: 'project.index', icon: BriefcaseIcon, current: route().current('project.*') },
  { name: 'Features', route: 'features.index', icon: PuzzlePieceIcon, current: route().current('features.*') },
  { name: 'Acceptance Criteria', route: 'acceptance-criteria.index', icon: ClipboardDocumentCheckIcon, current: route().current('acceptance-criteria.*') },
  { name: 'Test Runs', route: 'test-run.index', icon: BeakerIcon, current: route().current('test-run.*') },
  { name: 'Targets', route: 'target.index', icon: ViewfinderCircleIcon, current: route().current('target.*') },
  { name: 'Invoices', route: 'invoice.index', icon: DocumentTextIcon, current: route().current('invoice.*') },
  { name: 'Clients', route: 'client.index', icon: UsersIcon, current: route().current('client.*') },
  { name: 'Client Users', route: 'client-users.index', icon: UserGroupIcon, current: route().current('client-users.*') },
  { name: 'Login Sessions', route: 'client-login-sessions.index', icon: ChartBarIcon, current: route().current('client-login-sessions.*') },
]
</script>
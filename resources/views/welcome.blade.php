<x-app-layout>
    <section class="bg-white dark:bg-gray-900 h-screen">
        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
            <div class="mx-auto max-w-screen-md text-center mb-8 lg:mb-12">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Welcome to Attendance Application</h2>
                <p class="mb-5 font-light text-gray-500 sm:text-xl dark:text-gray-400">Please chose the room below</p>
            </div>
            <div class="space-y-8 lg:grid sm:gap-6 xl:gap-10 lg:space-y-0">
                <div class="flex flex-col p-6 mx-36 text-center text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 xl:p-8 dark:bg-gray-800 dark:text-white">
                    <a href="{{ route('pemrograman') }}" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm px-5 py-2.5 my-2 text-center dark:text-white  dark:focus:ring-primary-900">{{ __('Lab Pemrograman') }}</a>
                    <a href="{{ route('aplikasi-komputasi') }}" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm px-5 py-2.5 my-2 text-center dark:text-white  dark:focus:ring-primary-900">{{ __('Lab Aplikasi Komputasi') }}</a>
                    <a href="{{ route('aplikasi-profesional') }}" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm px-5 py-2.5 my-2 text-center dark:text-white  dark:focus:ring-primary-900">{{ __('Lab Aplikasi Profesional') }}</a>
                    <a href="{{ route('jaringan-komputer') }}" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm px-5 py-2.5 my-2 text-center dark:text-white  dark:focus:ring-primary-900">{{ __('Lab Jaringan Komputer') }}</a>
                    <a href="{{ route('sistem-informasi') }}" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm px-5 py-2.5 my-2 text-center dark:text-white  dark:focus:ring-primary-900">{{ __('Lab Sistem Informasi') }}</a>
                </div>
            </div>
        </div>
      </section>
</x-app-layout>

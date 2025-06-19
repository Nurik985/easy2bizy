<div>
	<div class="h-screen w-screen flex justify-center items-center ">
		<div class="grid gap-8">
			<div
					id="back-div"
					class="bg-gradient-to-r from-blue-500 to-purple-500 rounded-[26px] m-4"
			>
				<div
						class="border-[20px] border-transparent rounded-[20px]  bg-white shadow-lg xl:p-10 2xl:p-10 lg:p-10 md:p-10 sm:p-2 m-2"
				>
					<h1 class=" pb-6 font-bold dark:text-gray-600 text-5xl text-center cursor-default">
						<span style="color: #ff705d">Easy</span><span style="color: #006baf">Bizy</span>
					</h1>
					<p class="w-2xs mb-3 text-center text-gray-800" style="font-weight: 600; ">Сервис для автоматизации однотипных звонков вашим клиентам</p>
					<form wire:submit="auth" class="space-y-4">
						<div>
							<input
									wire:model="login"
									id="login"
									class="border-2 mt-2 p-3 bg-white text-gray-900 shadow-md placeholder-gray-500 focus:scale-102 ease-in-out duration-300 border-gray-500 rounded-lg w-full"
									type="text"
									placeholder="Логин"
							/>
						</div>
						<div>
							<input
									wire:model="password"
									id="password"
									class="border-2 mt-2 p-3 shadow-md bg-white text-gray-900  placeholder-gray-500 focus:scale-102 ease-in-out duration-300 border-gray-500 rounded-lg w-full"
									type="password"
									placeholder="Пароль"
							/>
						</div>
                        <div class="flex items-center justify-center ">
                            <div role="status" wire:loading>
                                <svg aria-hidden="true" class="mt-5 w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
						<button
								class="bg-gradient-to-r text-2xl cursor-pointer from-blue-500 to-purple-500 shadow-lg mt-6 p-2 text-white rounded-lg w-full hover:scale-105 hover:from-purple-500 hover:to-blue-500 transition duration-300 ease-in-out flex items-center justify-center border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700"
								type="submit"
                                wire:loading.remove wire:target="auth"
						>
                            <span >Войти</span>

						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

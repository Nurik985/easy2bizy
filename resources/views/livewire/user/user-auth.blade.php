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
						<button
								class="bg-gradient-to-r text-2xl cursor-pointer dark:text-gray-300 from-blue-500 to-purple-500 shadow-lg mt-6 p-2 text-white rounded-lg w-full hover:scale-105 hover:from-purple-500 hover:to-blue-500 transition duration-300 ease-in-out"
								type="submit"
						>
							войти
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Login Form (absolutely positioned) -->
<div
	id="login-container"
	class="modalForm">

	<form
		name="login-form"
		id="login-form"
		role="form"
		method="post"
		action="">

		<input
			type="text"
			name="username"
			id="username"
			class="form-control"
			placeholder="Username">

		<input
			type="password"
			name="password"
			id="password"
			class="form-control"
			placeholder="Password">

		<input
			type="submit"
			id="login-submit-btn"
			class="btn btn-md btn-primary"
			value="Login">

		<button
			id="loggingIn-btn"
			class="btn btn-md btn-primary login-msg"
			style="display:none;">
			Logging in...
		</button>

		<button
			id="login-failure-btn"
			class="btn btn-md btn-danger login-msg"
			style="display:none;">
			Login Failure
		</button>
	</form>
</div>

tailwind.config = {
	mode: 'jit',
	theme: {
		extend: {
			colors: {
				dataflowBackground: 'var(--bg-main)',
				dataflowSecondary: 'var(--bg-secondary)',
				dataflowText: 'var(--text-main)',
				dataflowTextSecondary: 'var(--text-secondary)',
				slateDarkest: '#0E0E0EFF',
				slateDark: '#1c1c1e',
			},
			boxShadow: {
				'neon-teal': '0 0 8px rgba(130, 130, 130, 0.3)',
				'neon-blue': '0 0 8px rgba(130, 130, 130, 0.3)',
			},
			spacing: {
				'128': '32rem',
				'144': '36rem',
			},
		},
	}
}

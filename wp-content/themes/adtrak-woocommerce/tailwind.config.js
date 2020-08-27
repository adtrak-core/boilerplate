module.exports = {
	purge: [],
	theme: {
		screens: {
			'2xs': '375px',
			'xs': '480px',
			'sm': '600px',
			'md': '768px',
			'lg': '1024px',
			'xl': '1280px',
			'2xl': '1400px',
			'3xl': '1600px',
			'4xl': '1900px',
		},
		textColor: {
			transparent: "transparent",
			white: "#fff",
			black: "#000",
			primary: "#000",
			secondary: "#333",
			"menu-active": "#77dd77"
		},
		backgroundColor: {
			primary: "#ffffff",
			secondary: "#f7f7f7",
			tertiary: "#eaeff3",
			link: "#228DFF"
		},
		blockSpacing: {
			px: "1px",
			"0": "0",
			"1": "5px",
			"2": "10px",
			"3": "15px",
			"4": "20px",
			"5": "25px",
			"6": "30px",
			"7": "35px",
			"8": "40px",
			"9": "45px",
			"10": "50px",
			"20": "100px",
			"24": "120px",
			"40": "200px",
			"-1": "-5px",
			"-2": "-10px",
			"-3": "-15px",
			"-4": "-20px",
			"-5": "-25px",
			"-6": "-30px",
			"-7": "-35px",
			"-8": "-40px",
			"-9": "-45px",
			"-10": "-50px",
			"-24": "-120px",
			auto: "auto"
		},
		spacing: {
			auto: "auto",
			"1/12": "8.33333333%",
			"2/12": "16.66666667%",
			"3/12": "25%",
			"4/12": "33.33333333%",
			"5/12": "41.66666667%",
			"6/12": "50%",
			"7/12": "58.33333333%",
			"8/12": "66.66666667%",
			"9/12": "75%",
			"10/12": "83.33333333%",
			"11/12": "91.66666667%",
			"12/12": "100%",
			"1/5": "20%",
			"2/5": "40%",
			"3/5": "60%",
			"4/5": "80%",
			"5/5": "100%",
			full: "100%"
		},
		width: theme => ({
			...theme("spacing"),
			screen: "100vw"
		}),
		height: theme => ({
			screen: "100vh",
			auto: "auto",
			full: "100%"
		}),
		minHeight: theme => ({
			screen: "100vh",
			auto: "auto"
		}),
		maxWidth: theme => ({
			...theme("spacing"),
			screen: "100vw"
		}),
		minWidth: theme => ({
			...theme("spacing"),
			screen: "100vw"
		}),
		margin: theme => ({
			...theme("blockSpacing")
		}),
		padding: theme => ({
			...theme("blockSpacing")
		}),
		extend: {}
	},
	variants: {},
	plugins: [],
	corePlugins: {
		container: false
	}
};

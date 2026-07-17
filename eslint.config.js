import js from '@eslint/js';
import reactHooks from 'eslint-plugin-react-hooks';
import reactRefresh from 'eslint-plugin-react-refresh';
import globals from 'globals';
import tseslint from 'typescript-eslint';

export default tseslint.config(
    { ignores: ['.template', 'node_modules', 'public/build', 'vendor'] },
    js.configs.recommended,
    ...tseslint.configs.recommended,
    {
        files: ['resources/js/**/*.{js,ts,tsx}', 'vite.config.js'],
        languageOptions: {
            globals: globals.browser,
        },
        plugins: {
            'react-hooks': reactHooks,
            'react-refresh': reactRefresh,
        },
        rules: {
            ...reactHooks.configs.recommended.rules,
            '@typescript-eslint/no-explicit-any': 'off',
            'react-refresh/only-export-components': 'off',
        },
    },
);

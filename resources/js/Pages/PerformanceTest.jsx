// resources/js/Pages/PerformanceTest.jsx

import React from "react";
import { Link, useForm, usePage } from "@inertiajs/react";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import PrimaryButton from "@/Components/PrimaryButton";
import InputError from "@/Components/InputError";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
const PerformanceTest = () => {
    const { props } = usePage();
    const { data, setData, post, processing, errors, reset } = useForm({
        url: "",
        platform: "Mobile",
    });

    const [showResult, setShowResult] = React.useState(false);

    const { performanceScore, testedUrl, platform, apiError, errorDetails } =
        props;

    performanceScore?.toFixed(2);

    const submit = (e) => {
        e.preventDefault();
        post("/performance-test", {
            onFinish: () => {
                reset("url");
                setShowResult(true);
            },
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Lighthouse Performance Test" />
            <div className="max-w-3xl mx-auto p-8 bg-white shadow-md rounded-md mt-8">
                <h2 className="text-3xl font-bold mb-6 text-gray-800">
                    Lighthouse Performance Test
                </h2>

                <form onSubmit={submit} className="space-y-6">
                    <div>
                        <InputLabel htmlFor="url" value="Website URL" />

                        <TextInput
                            id="url"
                            type="url"
                            name="url"
                            value={data.url}
                            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="https://example.com"
                            required
                            onChange={(e) => setData("url", e.target.value)}
                        />

                        <InputError message={errors.url} className="mt-2" />
                    </div>

                    <div>
                        <InputLabel htmlFor="platform" value="Platform" />

                        <select
                            id="platform"
                            name="platform"
                            value={data.platform}
                            onChange={(e) =>
                                setData("platform", e.target.value)
                            }
                            className="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="Mobile">Mobile</option>
                            <option value="Desktop">Desktop</option>
                        </select>

                        <InputError
                            message={errors.platform}
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <PrimaryButton
                            disabled={processing}
                            className="w-full flex items-center justify-center"
                        >
                            {processing ? (
                                <span className="flex items-center justify-center">
                                    <svg
                                        className="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            className="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            strokeWidth="4"
                                        ></circle>
                                        <path
                                            className="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                        ></path>
                                    </svg>
                                    Testing...
                                </span>
                            ) : (
                                "Run Performance Test"
                            )}
                        </PrimaryButton>
                    </div>
                </form>

                {apiError && (
                    <div className="mt-6 p-4 bg-red-100 text-red-800 rounded-md">
                        <h3 className="text-lg font-semibold">Error:</h3>
                        <p>{apiError}</p>
                        {errorDetails && (
                            <pre className="mt-2 text-sm text-red-600">
                                {JSON.stringify(errorDetails, null, 2)}
                            </pre>
                        )}
                    </div>
                )}

                {performanceScore !== null && showResult && (
                    <div className="mt-6 p-6 bg-green-100 text-green-800 rounded-md">
                        <h3 className="text-xl font-semibold mb-2">
                            Performance Score: {performanceScore}/100
                        </h3>
                        <div className="space-y-1">
                            <p>
                                <strong>Tested URL: </strong>
                                <Link href={testedUrl}>{testedUrl}</Link>
                            </p>
                            <p>
                                <strong>Platform:</strong> {platform}
                            </p>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
};

export default PerformanceTest;
